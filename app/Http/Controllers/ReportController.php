<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:view reports']);
    }

    /**
     * Daily report view.
     */
    public function daily(Request $request)
    {
        $user = Auth::user();
        $date = $request->get('date', today()->toDateString());

        $query = Transaction::with(['cashier', 'branch'])
            ->whereDate('transaction_date', $date)
            ->where('status', 'completed');

        if (!$user->hasRole('owner')) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        $summary = [
            'total_transactions' => $transactions->count(),
            'total_revenue' => $transactions->sum('total'),
            'total_tax' => $transactions->sum('tax'),
            'total_discount' => $transactions->sum('discount'),
            'avg_transaction' => $transactions->avg('total') ?? 0,
        ];

        $hourlyData = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourlyData[$hour] = [
                'hour' => sprintf('%02d:00', $hour),
                'count' => $transactions->filter(function ($t) use ($hour) {
                    return $t->transaction_date->hour == $hour;
                })->count(),
                'revenue' => $transactions->filter(function ($t) use ($hour) {
                    return $t->transaction_date->hour == $hour;
                })->sum('total'),
            ];
        }

        $topProducts = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereDate('transactions.transaction_date', $date)
            ->where('transactions.status', 'completed');

        if (!$user->hasRole('owner')) {
            $topProducts->where('transactions.branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $topProducts->where('transactions.branch_id', $request->branch_id);
        }

        $topProducts = $topProducts->select(
            'products.name',
            DB::raw('SUM(transaction_details.quantity) as total_quantity'),
            DB::raw('SUM(transaction_details.subtotal) as total_revenue')
        )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $branches = $user->hasRole('owner') ? Branch::all() : collect();

        return view('reports.daily', compact(
            'date',
            'transactions',
            'summary',
            'hourlyData',
            'topProducts',
            'branches'
        ));
    }
}