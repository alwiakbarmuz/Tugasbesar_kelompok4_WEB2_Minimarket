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
}