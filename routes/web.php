<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'branch.active', 'force.password'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('profile')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.change-password');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::resource('branches', BranchController::class)
        ->middleware('role:owner');
    Route::post('/branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])
        ->name('branches.toggle-status')
        ->middleware('role:owner');
    Route::post('/branches/{branch}/reset-passwords', [BranchController::class, 'resetBranchPasswords'])
        ->name('branches.reset-passwords')
        ->middleware('role:owner');

    Route::resource('products', ProductController::class);
    Route::post('/products/{product}/stock', [StockController::class, 'update'])
        ->name('products.stock');
    Route::get('/products/{product}/stock-history', [StockController::class, 'history'])
        ->name('products.stock-history');

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/{transaction}/print', [TransactionController::class, 'print'])->name('transactions.print');
        Route::post('/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    });

    Route::post('/cart/add', [TransactionController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/remove/{productId}', [TransactionController::class, 'removeFromCart'])->name('cart.remove');
    Route::delete('/cart/clear', [TransactionController::class, 'clearCart'])->name('cart.clear');
    Route::get('/product/{barcode}', [TransactionController::class, 'getProduct'])->name('product.by-barcode');

    Route::prefix('reports')->group(function () {
        Route::get('/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/export', [ReportController::class, 'export'])->name('reports.export');
    });
});

Route::middleware(['auth', 'role:owner', 'branch.active'])->prefix('audit')->group(function () {
    Route::get('/transactions', [AuditController::class, 'trashedTransactions'])
        ->name('audit.transactions');

    Route::post('/transactions/{id}/restore', [AuditController::class, 'restoreTransaction'])
        ->name('audit.transactions.restore');

    Route::delete('/transactions/{id}/force-delete', [AuditController::class, 'forceDeleteTransaction'])
        ->name('audit.transactions.force-delete');

    Route::get('/products', [AuditController::class, 'trashedProducts'])
        ->name('audit.products');

    Route::post('/products/{id}/restore', [AuditController::class, 'restoreProduct'])
        ->name('audit.products.restore');

    Route::delete('/products/{id}/force-delete', [AuditController::class, 'forceDeleteProduct'])
        ->name('audit.products.force-delete');
});

Route::fallback(function () {
    return redirect()->route('dashboard')
        ->with('error', 'Halaman yang Anda cari tidak ditemukan.');
});