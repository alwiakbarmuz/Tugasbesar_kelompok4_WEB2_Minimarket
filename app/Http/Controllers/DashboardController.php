<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:view dashboard']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Determine branch filter based on role
        if ($user->hasRole('owner')) {
            $branches = Branch::all();
            $transactionsQuery = Transaction::query();
            $productsQuery = Product::query();
            $selectedBranch = $request->get('branch_id', null);

            if ($selectedBranch) {
                $transactionsQuery->where('branch_id', $selectedBranch);
                $productsQuery->where('branch_id', $selectedBranch);
            }
        } else {
            $branches = collect();
            $transactionsQuery = Transaction::where('branch_id', $user->branch_id);
            $productsQuery = Product::where('branch_id', $user->branch_id);
            $selectedBranch = $user->branch_id;
        }

        // Today's statistics
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        $todayTransactions = (clone $transactionsQuery)->where('transaction_date', '>=', $today)->count();
        $todayRevenue = (clone $transactionsQuery)->where('transaction_date', '>=', $today)->sum('total');

        // Yesterday's statistics for trend
        $yesterdayTransactions = (clone $transactionsQuery)
            ->whereBetween('transaction_date', [$yesterday, $today])
            ->count();
        $yesterdayRevenue = (clone $transactionsQuery)
            ->whereBetween('transaction_date', [$yesterday, $today])
            ->sum('total');

        // Calculate trends
        $transactionsTrend = $this->calculateTrend($todayTransactions, $yesterdayTransactions);
        $revenueTrend = $this->calculateTrend($todayRevenue, $yesterdayRevenue);

        // Stock statistics
        $lowStockProducts = (clone $productsQuery)->whereRaw('stock <= min_stock AND stock > 0')->count();
        $outOfStockProducts = (clone $productsQuery)->where('stock', 0)->count();
        $totalStock = (clone $productsQuery)->sum('stock');
        $totalProducts = (clone $productsQuery)->count();
        $totalStockValue = (clone $productsQuery)->sum(DB::raw('stock * purchase_price'));

        // Products sold today
        $productsSoldToday = (clone $transactionsQuery)
            ->where('transaction_date', '>=', $today)
            ->with('details')
            ->get()
            ->sum(function ($transaction) {
                return $transaction->details->sum('quantity');
            });

        // Average transaction value
        $avgTransactionValue = (clone $transactionsQuery)->avg('total') ?? 0;

        // Recent transactions
        $recentTransactions = $transactionsQuery
            ->with(['cashier', 'branch'])
            ->latest()
            ->take(10)
            ->get();

        // Chart data for last 7 days
        $chartData = [
            'labels' => [],
            'values' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyTotal = (clone $transactionsQuery)
                ->whereDate('transaction_date', $date)
                ->sum('total');

            $chartData['labels'][] = $date->format('d M');
            $chartData['values'][] = $dailyTotal;
        }

        // ==========================================
        // DATA KHUSUS UNTUK WAREHOUSE
        // ==========================================
        if ($user->hasRole('warehouse')) {
            // Produk dengan stok menipis
            $lowStockProductList = (clone $productsQuery)
                ->whereRaw('stock <= min_stock')
                ->orderBy('stock', 'asc')
                ->paginate(10);

            // Data untuk chart stok per kategori
            $categoryStockData = [
                'labels' => [],
                'data' => []
            ];

            $categories = (clone $productsQuery)
                ->select('category', DB::raw('SUM(stock) as total_stock'))
                ->groupBy('category')
                ->get();

            foreach ($categories as $cat) {
                $categoryStockData['labels'][] = $cat->category;
                $categoryStockData['data'][] = $cat->total_stock;
            }

            return view('dashboard.index', compact(
                'todayTransactions',
                'todayRevenue',
                'lowStockProducts',
                'outOfStockProducts',
                'totalStock',
                'totalProducts',
                'productsSoldToday',
                'avgTransactionValue',
                'recentTransactions',
                'chartData',
                'branches',
                'selectedBranch',
                'transactionsTrend',
                'revenueTrend',
                'totalStockValue',
                'lowStockProductList',
                'categoryStockData'
            ));
        }

        // Return view untuk role lain
        return view('dashboard.index', compact(
            'todayTransactions',
            'todayRevenue',
            'lowStockProducts',
            'outOfStockProducts',
            'totalStock',
            'totalProducts',
            'productsSoldToday',
            'avgTransactionValue',
            'recentTransactions',
            'chartData',
            'branches',
            'selectedBranch',
            'transactionsTrend',
            'revenueTrend',
            'totalStockValue'
        ));
    }

    /**
     * Calculate percentage trend between two values
     */
    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $percentage = (($current - $previous) / $previous) * 100;
        $sign = $percentage >= 0 ? '+' : '';

        return $sign . round($percentage, 1) . '%';
    }
}