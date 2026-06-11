<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

/**
 * Class BranchController
 * @package App\Http\Controllers
 * @method void middleware(string|array $middleware, array $options = [])
 */
class BranchController extends Controller
{
    /**
     * Maximum allowed branches (sesuai kebutuhan Bapak Jayusman).
     */
    const MAX_BRANCHES = 5;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['role:owner'])->except(['index', 'show']);
    }

    /**
     * Display a listing of branches.
     */
    public function index(Request $request)
    {
        $query = Branch::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Filter active status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $branches = $query->orderBy('name')->paginate(10);

        // Statistics for each branch
        $stats = [];
        foreach ($branches as $branch) {
            $stats[$branch->id] = [
                'products_count' => Product::where('branch_id', $branch->id)->count(),
                'employees_count' => User::where('branch_id', $branch->id)->count(),
                'transactions_today' => Transaction::where('branch_id', $branch->id)
                    ->whereDate('transaction_date', today())
                    ->where('status', 'completed')
                    ->count(),
                'revenue_today' => Transaction::where('branch_id', $branch->id)
                    ->whereDate('transaction_date', today())
                    ->where('status', 'completed')
                    ->sum('total'),
                'transactions_total' => Transaction::where('branch_id', $branch->id)
                    ->where('status', 'completed')
                    ->count(),
                'revenue_total' => Transaction::where('branch_id', $branch->id)
                    ->where('status', 'completed')
                    ->sum('total'),
            ];
        }

        $currentBranchesCount = Branch::count();
        $maxBranches = self::MAX_BRANCHES;
        $remainingSlots = $maxBranches - $currentBranchesCount;
        $isFull = $currentBranchesCount >= $maxBranches;

        return view('branches.index', compact('branches', 'stats', 'currentBranchesCount', 'maxBranches', 'remainingSlots', 'isFull'));
    }

    /**
     * Show form to create new branch.
     */
    public function create()
    {
        $currentBranchesCount = Branch::count();
        $maxBranches = self::MAX_BRANCHES;
        $remainingSlots = $maxBranches - $currentBranchesCount;
        $isFull = $currentBranchesCount >= $maxBranches;

        return view('branches.create', compact('currentBranchesCount', 'maxBranches', 'remainingSlots', 'isFull'));
    }

    /**
     * Store a newly created branch with auto-generated accounts.
     */
    public function store(Request $request)
    {
        // Cek batas maksimal cabang
        $currentCount = Branch::count();
        $maxBranches = self::MAX_BRANCHES;

        if ($currentCount >= $maxBranches) {
            return redirect()->route('branches.index')
                ->with('error', "Tidak dapat menambahkan cabang baru. Maksimal cabang adalah {$maxBranches} cabang. Saat ini sudah ada {$currentCount} cabang.");
        }

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:branches',
            'name' => 'required|string|max:100',
            'city' => 'required|string|max:50',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::beginTransaction();

        try {
            // 1. Create branch
            $branch = Branch::create($validated);

            // 2. Auto generate accounts for this branch
            $this->generateBranchAccounts($branch);

            DB::commit();

            $newCount = $currentCount + 1;
            $remainingSlots = $maxBranches - $newCount;

            $message = "Cabang {$branch->name} berhasil ditambahkan beserta akun-akun karyawan. Password default: password123";

            if ($remainingSlots > 0) {
                $message .= " | Sisa slot cabang: {$remainingSlots} dari {$maxBranches}";
            } else {
                $message .= " | Slot cabang sudah penuh ({$maxBranches}/{$maxBranches})";
            }

            return redirect()->route('branches.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membuat cabang: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate default accounts for a new branch.
     */
    private function generateBranchAccounts(Branch $branch)
    {
        $defaultPassword = 'password123';

        // Data akun default per cabang
        $accounts = [
            [
                'name' => "Manager {$branch->name}",
                'email' => "manager.{$branch->code}@minimarket.com",
                'role' => 'manager',
            ],
            [
                'name' => "Supervisor {$branch->name}",
                'email' => "supervisor.{$branch->code}@minimarket.com",
                'role' => 'supervisor',
            ],
            [
                'name' => "Warehouse {$branch->name}",
                'email' => "warehouse.{$branch->code}@minimarket.com",
                'role' => 'warehouse',
            ],
            [
                'name' => "Cashier 1 {$branch->name}",
                'email' => "cashier1.{$branch->code}@minimarket.com",
                'role' => 'cashier',
            ],
            [
                'name' => "Cashier 2 {$branch->name}",
                'email' => "cashier2.{$branch->code}@minimarket.com",
                'role' => 'cashier',
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::create([
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => Hash::make($defaultPassword),
                'branch_id' => $branch->id,
                'email_verified_at' => now(),
                'must_change_password' => true,
            ]);

            $user->assignRole($account['role']);
        }
    }

    /**
     * Display specific branch details.
     */
    public function show(Branch $branch)
    {
        // Statistics
        $stats = [
            'products_count' => Product::where('branch_id', $branch->id)->count(),
            'employees_count' => User::where('branch_id', $branch->id)->count(),
            'transactions_count' => Transaction::where('branch_id', $branch->id)->count(),
            'total_revenue' => Transaction::where('branch_id', $branch->id)->sum('total'),
            'revenue_today' => Transaction::where('branch_id', $branch->id)
                ->whereDate('transaction_date', today())
                ->sum('total'),
            'transactions_today' => Transaction::where('branch_id', $branch->id)
                ->whereDate('transaction_date', today())
                ->count(),
            'avg_transaction' => Transaction::where('branch_id', $branch->id)->avg('total') ?? 0,
        ];

        // Get users in this branch
        $users = User::where('branch_id', $branch->id)
            ->with('roles')
            ->get();

        // Recent transactions
        $recentTransactions = Transaction::where('branch_id', $branch->id)
            ->with('cashier')
            ->latest()
            ->take(10)
            ->get();

        // Low stock products
        $lowStockProducts = Product::where('branch_id', $branch->id)
            ->whereRaw('stock <= min_stock')
            ->take(10)
            ->get();

        // Monthly chart data
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData['labels'][] = $date->format('M Y');
            $monthlyData['revenue'][] = Transaction::where('branch_id', $branch->id)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('total');
        }

        return view('branches.show', compact('branch', 'stats', 'users', 'recentTransactions', 'lowStockProducts', 'monthlyData'));
    }

    /**
     * Show form to edit branch.
     */
    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('branches')->ignore($branch->id)],
            'name' => 'required|string|max:100',
            'city' => 'required|string|max:50',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::beginTransaction();

        try {
            $oldName = $branch->name;
            $branch->update($validated);

            // If branch name changed, update related account names
            if ($oldName != $branch->name) {
                $this->updateAccountNames($branch);
            }

            DB::commit();

            return redirect()->route('branches.index')
                ->with('success', "Cabang {$branch->name} berhasil diupdate");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal update cabang: ' . $e->getMessage());
        }
    }

    /**
     * Update account names when branch name changes.
     */
    private function updateAccountNames(Branch $branch)
    {
        $users = User::where('branch_id', $branch->id)->get();

        foreach ($users as $user) {
            $role = $user->roles->first()->name ?? 'staff';

            if ($role === 'cashier') {
                preg_match('/Cashier (\d+)/', $user->name, $matches);
                $number = $matches[1] ?? '1';
                $newName = "Cashier {$number} {$branch->name}";
            } else {
                $newName = ucfirst($role) . " {$branch->name}";
            }

            $user->update(['name' => $newName]);
        }
    }

    /**
     * Delete the specified branch and its associated accounts.
     */
    public function destroy(Branch $branch)
    {
        // Check if branch has any transactions
        $hasTransactions = Transaction::where('branch_id', $branch->id)->exists();
        $hasProducts = Product::where('branch_id', $branch->id)->exists();

        if ($hasTransactions || $hasProducts) {
            return redirect()->route('branches.index')
                ->with('error', "Cabang {$branch->name} tidak dapat dihapus karena masih memiliki data transaksi atau produk");
        }

        DB::beginTransaction();

        try {
            // Delete all users in this branch
            User::where('branch_id', $branch->id)->delete();

            // Delete branch
            $branchName = $branch->name;
            $branch->delete();

            DB::commit();

            return redirect()->route('branches.index')
                ->with('success', "Cabang {$branchName} beserta semua akun karyawan berhasil dihapus");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus cabang: ' . $e->getMessage());
        }
    }

    /**
     * Toggle branch active status.
     */
    public function toggleStatus(Branch $branch)
    {
        $branch->update(['is_active' => !$branch->is_active]);

        $status = $branch->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Cabang {$branch->name} berhasil {$status}");
    }

    /**
     * Reset all passwords for a branch.
     */
    public function resetBranchPasswords(Branch $branch)
    {
        $defaultPassword = 'password123';
        $users = User::where('branch_id', $branch->id)->get();

        $count = 0;
        foreach ($users as $user) {
            $user->update([
                'password' => Hash::make($defaultPassword),
                'must_change_password' => true,
            ]);
            $count++;
        }

        return redirect()->back()
            ->with('success', "Password {$count} akun di cabang {$branch->name} telah direset ke: {$defaultPassword}");
    }
}