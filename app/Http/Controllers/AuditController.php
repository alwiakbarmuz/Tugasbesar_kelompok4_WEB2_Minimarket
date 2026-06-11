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
}