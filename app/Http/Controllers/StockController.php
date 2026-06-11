<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage stock');
    }

    public function update(Request $request, Product $product)
    {
        $user = Auth::user();

        if (!$user->hasRole('owner') && $product->branch_id != $user->branch_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required_if:type,in,out|integer|min:1',
            'stock' => 'required_if:type,adjustment|integer|min:0',
            'note' => 'nullable|string',
        ]);

        if ($validated['type'] == 'adjustment') {
            $product->updateStock($validated['stock'], 'adjustment', $user->id, $validated['note'] ?? null);
            $message = "Stok disesuaikan menjadi {$validated['stock']}";
        } else {
            $quantity = $validated['quantity'];
            $product->updateStock($quantity, $validated['type'], $user->id, $validated['note'] ?? null);
            $message = ($validated['type'] == 'in')
                ? "Stok masuk {$quantity} {$product->unit}"
                : "Stok keluar {$quantity} {$product->unit}";
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'new_stock' => $product->stock
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    public function history(Product $product)
    {
        $user = Auth::user();

        if (!$user->hasRole('owner') && $product->branch_id != $user->branch_id) {
            abort(403);
        }

        $logs = $product->stockLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('products.stock-history', compact('product', 'logs'));
    }
}