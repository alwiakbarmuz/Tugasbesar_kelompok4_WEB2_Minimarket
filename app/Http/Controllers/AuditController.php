<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['role:owner']); // Hanya owner yang bisa akses audit
    }

    /**
     * Display trashed transactions (soft deleted).
     */
    public function trashedTransactions(Request $request)
    {
        // Gunakan onlyTrashed() untuk mengambil data yang sudah di soft delete
        $query = Transaction::onlyTrashed()
            ->with(['branch', 'cashier', 'deletedBy'])
            ->orderBy('deleted_at', 'desc');

        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('deleted_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('deleted_at', '<=', $request->date_to);
        }

        // Search by invoice
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->paginate(20);
        $branches = Branch::all();

        return view('audit.trashed-transactions', compact('transactions', 'branches'));
    }
    /**
     * Restore a soft deleted transaction.
     */
    public function restoreTransaction($id)
    {
        try {
            $user = Auth::user();

            $transaction = Transaction::withTrashed()->findOrFail($id);

            if ($transaction->status !== 'cancelled') {
                return response()->json(['error' => 'Transaksi tidak dapat dikembalikan'], 400);
            }

            DB::beginTransaction();

            // Cek stok sebelum restore
            foreach ($transaction->details as $detail) {
                $product = $detail->product;

                // Cek apakah produk masih ada
                if (!$product) {
                    throw new \Exception("Produk dengan ID {$detail->product_id} tidak ditemukan");
                }

                // Cek apakah stok cukup untuk dikurangi kembali
                if ($product->stock < $detail->quantity) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi untuk mengembalikan transaksi");
                }

                $product->updateStock(
                    $detail->quantity,
                    'out',
                    $user->id,
                    "Pengembalian transaksi (restore): {$transaction->invoice_number}"
                );
            }

            // Restore transaction (mengisi ulang deleted_at = null)
            $transaction->restore();
            $transaction->update([
                'status' => 'completed',
                'deleted_by' => null,
                'delete_reason' => null,
            ]);

            DB::commit();

            Log::info('Transaction restored', [
                'transaction_id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number,
                'restored_by' => $user->id,
                'restored_by_name' => $user->name
            ]);

            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dikembalikan']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore transaction', [
                'transaction_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Permanently delete a transaction (force delete).
     */
    public function forceDeleteTransaction($id)
    {
        try {
            $transaction = Transaction::withTrashed()->findOrFail($id);
            $invoiceNumber = $transaction->invoice_number;

            // Log sebelum hapus permanen
            Log::warning('Permanent deletion of transaction', [
                'transaction_id' => $transaction->id,
                'invoice_number' => $invoiceNumber,
                'deleted_by' => Auth::user()->id,
                'deleted_by_name' => Auth::user()->name,
                'original_deleted_at' => $transaction->deleted_at,
                'delete_reason' => $transaction->delete_reason,
            ]);

            $transaction->forceDelete();

            return response()->json(['success' => true, 'message' => "Transaksi {$invoiceNumber} telah dihapus permanen"]);
        } catch (\Exception $e) {
            Log::error('Failed to force delete transaction', [
                'transaction_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Display trashed products (soft deleted).
     */
    public function trashedProducts(Request $request)
    {
        $query = Product::onlyTrashed()
            ->with(['branch', 'deletedBy'])
            ->orderBy('deleted_at', 'desc');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(20);
        $branches = Branch::all();

        return view('audit.trashed-products', compact('products', 'branches'));
    }

    /**
     * Restore a soft deleted product.
     */
    public function restoreProduct($id)
    {
        try {
            $user = Auth::user();

            $product = Product::withTrashed()->findOrFail($id);
            $productName = $product->name;

            // Cek apakah produk yang di-restore sudah pernah bertransaksi?
            $hasTransactions = $product->transactionDetails()->exists();

            // Restore product (mengisi deleted_at = null)
            $product->restore();

            // Hapus tanda deleted_by jika ada
            $product->deleted_by = null;
            $product->save();

            Log::info('Product restored', [
                'product_id' => $product->id,
                'product_name' => $productName,
                'restored_by' => $user->id,
                'restored_by_name' => $user->name,
                'had_transactions' => $hasTransactions
            ]);

            return response()->json(['success' => true, 'message' => "Produk {$productName} berhasil dikembalikan"]);
        } catch (\Exception $e) {
            Log::error('Failed to restore product', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Permanently delete a product (force delete).
     */
    public function forceDeleteProduct($id)
    {
        try {
            $user = Auth::user();

            $product = Product::withTrashed()->findOrFail($id);
            $productName = $product->name;

            // Log sebelum hapus permanen
            Log::warning('Permanent deletion of product', [
                'product_id' => $product->id,
                'product_name' => $productName,
                'barcode' => $product->barcode,
                'deleted_by' => $user->id,
                'deleted_by_name' => $user->name,
                'original_deleted_at' => $product->deleted_at,
                'stock' => $product->stock,
                'branch_id' => $product->branch_id
            ]);

            $product->forceDelete();

            return response()->json(['success' => true, 'message' => "Produk {$productName} telah dihapus permanen"]);
        } catch (\Exception $e) {
            Log::error('Failed to force delete product', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
