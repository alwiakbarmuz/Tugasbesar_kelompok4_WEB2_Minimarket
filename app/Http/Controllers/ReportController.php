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

    /**
     * Monthly report view.
     */
    public function monthly(Request $request)
    {
        $user = Auth::user();
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $query = Transaction::with(['branch'])
            ->whereBetween('transaction_date', [$startDate, $endDate . ' 23:59:59'])
            ->where('status', 'completed');

        if (!$user->hasRole('owner')) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $dailyData = [];
        $daysInMonth = date('t', strtotime($startDate));

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = date('Y-m-d', strtotime("{$year}-{$month}-{$day}"));
            $dayTransactions = (clone $query)->whereDate('transaction_date', $date)->get();

            $dailyData[] = [
                'date' => $day,
                'date_formatted' => date('d M Y', strtotime($date)),
                'count' => $dayTransactions->count(),
                'revenue' => $dayTransactions->sum('total'),
            ];
        }

        $summary = [
            'total_transactions' => (clone $query)->count(),
            'total_revenue' => (clone $query)->sum('total'),
            'total_tax' => (clone $query)->sum('tax'),
            'total_discount' => (clone $query)->sum('discount'),
            'avg_daily_transactions' => $daysInMonth > 0 ? round((clone $query)->count() / $daysInMonth, 2) : 0,
            'avg_daily_revenue' => $daysInMonth > 0 ? round((clone $query)->sum('total') / $daysInMonth, 2) : 0,
            'best_day' => !empty($dailyData) ? collect($dailyData)->sortByDesc('revenue')->first() : null,
        ];

        $branchComparison = [];
        if ($user->hasRole('owner') && !$request->filled('branch_id')) {
            $branches = Branch::all();
            foreach ($branches as $branch) {
                $branchTransactions = Transaction::where('branch_id', $branch->id)
                    ->whereBetween('transaction_date', [$startDate, $endDate . ' 23:59:59'])
                    ->where('status', 'completed')
                    ->get();

                $branchComparison[] = [
                    'branch' => $branch->name,
                    'transactions' => $branchTransactions->count(),
                    'revenue' => $branchTransactions->sum('total'),
                ];
            }
        }

        // FIX: Ambil tahun dari semua transaksi, bukan hanya yang sudah difilter
        $yearsQuery = Transaction::select(DB::raw('DISTINCT YEAR(transaction_date) as year'))
            ->where('status', 'completed')
            ->orderBy('year', 'desc');

        // Untuk non-owner, filter berdasarkan cabang
        if (!$user->hasRole('owner')) {
            $yearsQuery->where('branch_id', $user->branch_id);
        }

        $availableYears = $yearsQuery->pluck('year');

        // Jika tidak ada tahun dari query, gunakan tahun sekarang
        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y')]);
        }

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $branches = $user->hasRole('owner') ? Branch::all() : collect();

        return view('reports.monthly', compact(
            'year',
            'month',
            'dailyData',
            'summary',
            'branchComparison',
            'availableYears',
            'months',
            'branches'
        ));
    }

    /**
     * Stock report view.
     */
    public function stock(Request $request)
    {
        $user = Auth::user();

        $query = Product::with('branch');

        if (!$user->hasRole('owner')) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereRaw('stock <= min_stock AND stock > 0');
                    break;
                case 'out':
                    $query->where('stock', 0);
                    break;
                case 'good':
                    $query->whereRaw('stock > min_stock');
                    break;
            }
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(20);

        $summary = [
            'total_products' => (clone $query)->count(),
            'total_stock_value' => (clone $query)->sum(DB::raw('stock * purchase_price')),
            'total_retail_value' => (clone $query)->sum(DB::raw('stock * price')),
            'low_stock_count' => (clone $query)->whereRaw('stock <= min_stock AND stock > 0')->count(),
            'out_of_stock_count' => (clone $query)->where('stock', 0)->count(),
        ];

        $categories = (clone $query)
            ->select('category', DB::raw('COUNT(*) as total'))
            ->groupBy('category')
            ->get();

        $branches = $user->hasRole('owner') ? Branch::all() : collect();

        return view('reports.stock', compact('products', 'summary', 'categories', 'branches'));
    }
}