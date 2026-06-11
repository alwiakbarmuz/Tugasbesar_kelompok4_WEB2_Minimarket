<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBranchActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Owner tetap bisa login (tidak terikat cabang)
        if ($user->hasRole('owner')) {
            return $next($request);
        }

        // Cek apakah user memiliki cabang dan cabang tersebut aktif
        if ($user->branch && !$user->branch->is_active) {
            Auth::logout();

            return redirect()->route('login')
                ->with('error', 'Cabang Anda sedang tidak aktif. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}