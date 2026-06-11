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
}