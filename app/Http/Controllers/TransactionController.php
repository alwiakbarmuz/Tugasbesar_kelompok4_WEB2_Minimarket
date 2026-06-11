<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Class TransactionController
 * @package App\Http\Controllers
 * @method void middleware(string|array $middleware, array $options = [])
 */
class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view transactions')->only(['index', 'show']);
        $this->middleware('permission:create transactions')->only(['create', 'store']);
        $this->middleware('permission:print transactions')->only(['print']);
        $this->middleware('permission:cancel transactions')->only(['cancel']);
    }

    /**
     * Display list of transactions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Transaction::with(['cashier', 'branch']);

        // Filter by branch (owner can see all, others only their branch)
        if (!$user->hasRole('owner')) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Date filters
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        // Today filter
        if ($request->has('today')) {
            $query->whereDate('transaction_date', today());
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by invoice number
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', "%{$request->search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'transaction_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $transactions = $query->paginate(15);

        // Summary statistics
        $summary = [
            'total_transactions' => $query->count(),
            'total_revenue' => $query->sum('total'),
            'avg_transaction' => $query->avg('total') ?? 0,
            'total_tax' => $query->sum('tax'),
            'total_discount' => $query->sum('discount'),
        ];

        $branches = $user->hasRole('owner') ? Branch::all() : collect();

        return view('transactions.index', compact('transactions', 'summary', 'branches'));
    }

    /**
     * Show form to create new transaction.
     */
    public function create()
    {
        $user = Auth::user();

        // Get products for the branch
        $products = Product::where('branch_id', $user->branch_id)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        // Get branch info - FIX: Handle jika user tidak memiliki branch
        if ($user->branch_id) {
            $branch = Branch::find($user->branch_id);
        } else {
            // Untuk owner yang tidak memiliki branch
            $branch = null;
        }

        // Get cart from session (for draft transactions)
        $cart = session()->get('cart', []);

        return view('transactions.create', compact('products', 'branch', 'cart'));
    }

    /**
     * Store a new transaction.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi request
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'cash' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $items = [];
            $productsToUpdate = [];

            // Process each item
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check if user has access to this product's branch
                if (!$user->hasRole('owner') && $product->branch_id != $user->branch_id) {
                    throw new \Exception('Anda tidak memiliki akses ke produk ini');
                }

                // Check stock availability
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock}");
                }

                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $itemSubtotal,
                ];

                $productsToUpdate[$product->id] = [
                    'product' => $product,
                    'quantity' => $item['quantity']
                ];
            }

            // Calculate totals
            $tax = (int) round($subtotal * 0.11);
            $discount = 0;
            $total = $subtotal + $tax - $discount;
            $cash = $request->cash;
            $change = $cash - $total;

            if ($change < 0) {
                throw new \Exception('Uang tunai kurang dari total belanja');
            }

            // Create transaction
            $transaction = Transaction::create([
                'branch_id' => $user->branch_id ?? $productsToUpdate[array_key_first($productsToUpdate)]['product']->branch_id,
                'cashier_id' => $user->id,
                'transaction_date' => now(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'cash' => $cash,
                'change' => $change,
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            // Create transaction details and update stock
            foreach ($items as $item) {
                $transaction->details()->create($item);

                // Update product stock
                $product = $productsToUpdate[$item['product_id']]['product'];
                $product->updateStock(
                    $item['quantity'],
                    'out',
                    $user->id,
                    "Transaksi: {$transaction->invoice_number}"
                );
            }

            DB::commit();

            // Clear cart session
            session()->forget('cart');

            // Return JSON response for AJAX request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('dashboard'),
                    'message' => 'Transaksi berhasil!'
                ]);
            }

            // For non-AJAX requests - Redirect ke DASHBOARD
            return redirect()->route('dashboard')
                ->with('success', 'Transaksi berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Add item to cart (AJAX for POS system).
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $product = Product::where('id', $request->product_id)
            ->where('branch_id', $user->branch_id)
            ->firstOrFail();

        if ($product->stock < $request->quantity) {
            return response()->json([
                'error' => "Stok tidak mencukupi. Tersedia: {$product->stock}"
            ], 422);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $request->quantity,
                'subtotal' => $product->price * $request->quantity,
            ];
        }

        // Recalculate subtotal
        $cart[$product->id]['subtotal'] = $cart[$product->id]['price'] * $cart[$product->id]['quantity'];

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => array_sum(array_column($cart, 'subtotal'))
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => array_sum(array_column($cart, 'subtotal'))
        ]);
    }

    /**
     * Clear entire cart.
     */
    public function clearCart()
    {
        session()->forget('cart');

        return response()->json(['success' => true]);
    }

    /**
     * Display transaction details.
     */
    public function show(Transaction $transaction)
    {
        $user = Auth::user();

        // Check access
        if (!$user->hasRole('owner') && $transaction->branch_id != $user->branch_id) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini');
        }

        $transaction->load(['details.product', 'cashier', 'branch']);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Print transaction receipt.
     */
    public function print(Transaction $transaction)
    {
        $user = Auth::user();

        // Check access
        if (!$user->hasRole('owner') && $transaction->branch_id != $user->branch_id) {
            abort(403);
        }

        $transaction->load(['details.product', 'cashier', 'branch']);

        return view('transactions.print', compact('transaction'));
    }

    /**
     * Cancel a transaction (soft delete).
     */
    public function cancel(Request $request, Transaction $transaction)
    {
        $user = Auth::user();

        // Cek apakah request dari AJAX/JSON
        $isAjax = $request->ajax() || $request->expectsJson();

        try {
            // Check access
            if (!$user->hasRole('owner') && $transaction->branch_id != $user->branch_id) {
                if ($isAjax) {
                    return response()->json(['error' => 'Anda tidak memiliki akses'], 403);
                }
                abort(403);
            }

            // Check if already cancelled
            if ($transaction->status === 'cancelled') {
                if ($isAjax) {
                    return response()->json(['error' => 'Transaksi sudah dibatalkan sebelumnya'], 422);
                }
                return redirect()->back()->with('error', 'Transaksi sudah dibatalkan sebelumnya');
            }

            // Get cancel reason
            if ($isAjax) {
                $data = $request->json()->all();
                $reason = $data['cancel_reason'] ?? null;
            } else {
                $reason = $request->input('cancel_reason');
            }

            // Validate reason
            if (!$reason || strlen($reason) < 5) {
                if ($isAjax) {
                    return response()->json(['error' => 'Alasan pembatalan harus diisi minimal 5 karakter'], 422);
                }
                return redirect()->back()->with('error', 'Alasan pembatalan harus diisi minimal 5 karakter');
            }

            DB::beginTransaction();

            // Restore stock for each product
            foreach ($transaction->details as $detail) {
                $product = $detail->product;
                $product->updateStock(
                    $detail->quantity,
                    'in',
                    $user->id,
                    "Pembatalan transaksi: {$transaction->invoice_number} - Alasan: {$reason}"
                );
            }

            // Update transaction status dan soft delete
            $transaction->update([
                'status' => 'cancelled',
                'deleted_by' => $user->id,
                'delete_reason' => $reason,
            ]);

            // INI YANG PENTING: Panggil method delete() untuk soft delete
            $transaction->delete();  // <- Baris ini mengisi deleted_at

            DB::commit();

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => "Transaksi {$transaction->invoice_number} berhasil dibatalkan"
                ]);
            }

            return redirect()->route('transactions.index')
                ->with('success', "Transaksi {$transaction->invoice_number} berhasil dibatalkan");
        } catch (\Exception $e) {
            DB::rollBack();

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Restore a cancelled transaction (admin only).
     */
    public function restore($id)
    {
        $user = Auth::user();

        // Only owner can restore
        if (!$user->hasRole('owner')) {
            abort(403, 'Hanya owner yang dapat mengembalikan transaksi');
        }

        $transaction = Transaction::withTrashed()->findOrFail($id);

        if ($transaction->status !== 'cancelled') {
            return redirect()->back()->with('error', 'Transaksi tidak dapat dikembalikan');
        }

        DB::beginTransaction();

        try {
            // Deduct stock again
            foreach ($transaction->details as $detail) {
                $product = $detail->product;
                $product->updateStock(
                    $detail->quantity,
                    'out',
                    $user->id,
                    "Pengembalian transaksi: {$transaction->invoice_number}"
                );
            }

            // Restore transaction
            $transaction->restore();
            $transaction->update([
                'status' => 'completed',
                'deleted_by' => null,
                'delete_reason' => null,
            ]);

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', "Transaksi {$transaction->invoice_number} berhasil dikembalikan");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengembalikan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a transaction (admin only).
     */
    public function forceDelete($id)
    {
        $user = Auth::user();

        // Only owner can force delete
        if (!$user->hasRole('owner')) {
            abort(403);
        }

        $transaction = Transaction::withTrashed()->findOrFail($id);

        // Log before permanent deletion
        \Log::warning('Permanent deletion of transaction', [
            'transaction_id' => $transaction->id,
            'invoice_number' => $transaction->invoice_number,
            'deleted_by' => $user->id,
            'deleted_by_name' => $user->name,
            'original_deleted_at' => $transaction->deleted_at,
            'delete_reason' => $transaction->delete_reason,
        ]);

        $transaction->forceDelete();

        return redirect()->route('transactions.index')
            ->with('warning', "Transaksi {$transaction->invoice_number} telah dihapus permanen dari sistem");
    }

    /**
     * Get product details for POS (AJAX).
     */
    public function getProduct($barcode)
    {
        $user = Auth::user();

        $product = Product::where('barcode', $barcode)
            ->where('branch_id', $user->branch_id)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        if ($product->stock <= 0) {
            return response()->json(['error' => 'Produk sedang habis'], 422);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'unit' => $product->unit,
        ]);
    }
}