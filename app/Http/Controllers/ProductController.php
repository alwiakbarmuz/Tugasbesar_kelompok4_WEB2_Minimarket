<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view products')->only(['index', 'show']);
        $this->middleware('permission:create products')->only(['create', 'store']);
        $this->middleware('permission:edit products')->only(['edit', 'update']);
        $this->middleware('permission:delete products')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Product::with('branch');

        if (!$user->hasRole('owner')) {
            $query->where('branch_id', $user->branch_id);
        }

        // Filter by branch
        if ($request->filled('branch_id') && $user->hasRole('owner')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status == 'low') {
                $query->whereRaw('stock <= min_stock');
            } elseif ($request->stock_status == 'out') {
                $query->where('stock', 0);
            }
        }

        $products = $query->orderBy('name')->paginate(15);

        $branches = $user->hasRole('owner') ? Branch::all() : collect();
        $categories = Product::distinct()->pluck('category');

        return view('products.index', compact('products', 'branches', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $branches = $user->hasRole('owner') ? Branch::all() : collect([$user->branch]);

        return view('products.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|unique:products',
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
            'purchase_price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $user = Auth::user();
        if (!$user->hasRole('owner') && $validated['branch_id'] != $user->branch_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke cabang tersebut');
        }

        $product = Product::create($validated);

        // Create stock log for initial stock
        if ($product->stock > 0) {
            $product->updateStock($product->stock, 'in', $user->id, 'Stok awal');
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $user = Auth::user();

        // Check access - user can only view products from their branch (unless owner)
        if (!$user->hasRole('owner') && $product->branch_id != $user->branch_id) {
            abort(403, 'Anda tidak memiliki akses ke produk ini');
        }

        // Load relationships
        $product->load(['branch', 'stockLogs' => function ($query) {
            $query->latest()->limit(10);
        }]);

        // Get stock history for chart
        $stockHistory = $product->stockLogs()
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get()
            ->reverse();

        // Calculate profit margin
        $profit = $product->price - $product->purchase_price;
        $margin = $product->purchase_price > 0 ? ($profit / $product->purchase_price) * 100 : 0;

        // Get recent transactions for this product
        $recentTransactions = $product->transactionDetails()
            ->with('transaction')
            ->latest()
            ->limit(5)
            ->get();

        return view('products.show', compact('product', 'stockHistory', 'profit', 'margin', 'recentTransactions'));
    }

    public function edit(Product $product)
    {
        $user = Auth::user();
        if (!$user->hasRole('owner') && $product->branch_id != $user->branch_id) {
            abort(403);
        }

        $branches = $user->hasRole('owner') ? Branch::all() : collect([$user->branch]);

        return view('products.edit', compact('product', 'branches'));
    }

    public function update(Request $request, Product $product)
    {
        $user = Auth::user();
        if (!$user->hasRole('owner') && $product->branch_id != $user->branch_id) {
            abort(403);
        }

        $validated = $request->validate([
            'barcode' => 'required|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
            'purchase_price' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    /**
     * Remove the specified product (soft delete).
     */
    public function destroy(Product $product)
    {
        $user = Auth::user();

        if (!$user->hasRole('owner') && $product->branch_id != $user->branch_id) {
            abort(403);
        }

        // Check if product has transactions
        $hasTransactions = $product->transactionDetails()->exists();

        if ($hasTransactions) {
            // Soft delete only
            $product->deleted_by = $user->id;
            $product->save();
            $product->delete();

            return redirect()->route('products.index')
                ->with('warning', "Produk {$product->name} disembunyikan karena sudah pernah bertransaksi. Hubungi admin untuk menghapus permanen.");
        }

        // If no transactions, can force delete
        $product->forceDelete();

        return redirect()->route('products.index')
            ->with('success', "Produk {$product->name} berhasil dihapus");
    }

    /**
     * Restore a soft deleted product.
     */
    public function restore($id)
    {
        $user = Auth::user();

        if (!$user->hasRole('owner')) {
            abort(403);
        }

        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('products.index')
            ->with('success', "Produk {$product->name} berhasil dikembalikan");
    }

    /**
     * Permanently delete a product (admin only).
     */
    public function forceDelete($id)
    {
        $user = Auth::user();

        if (!$user->hasRole('owner')) {
            abort(403);
        }

        $product = Product::withTrashed()->findOrFail($id);
        $productName = $product->name;
        $product->forceDelete();

        return redirect()->route('products.index')
            ->with('warning', "Produk {$productName} telah dihapus permanen dari sistem");
    }

    /**
     * List soft deleted products.
     */
    public function trashed()
    {
        $user = Auth::user();

        $query = Product::onlyTrashed()->with(['branch', 'deletedBy']);

        if (!$user->hasRole('owner')) {
            $query->where('branch_id', $user->branch_id);
        }

        $products = $query->orderBy('deleted_at', 'desc')->paginate(20);

        return view('products.trashed', compact('products'));
    }
}