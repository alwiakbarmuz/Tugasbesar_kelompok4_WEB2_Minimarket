<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForceChangePassword
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Daftar route yang diizinkan tanpa harus ganti password
        $allowedRoutes = [
            'profile.edit',
            'profile.change-password',
            'logout',
            'profile.update',
            'password.confirm',
        ];

        $currentRoute = $request->route()->getName();

        // Log untuk debugging
        \Log::info('ForcePassword Middleware', [
            'user_id' => $user->id,
            'must_change_password' => $user->must_change_password,
            'current_route' => $currentRoute,
            'is_allowed' => in_array($currentRoute, $allowedRoutes)
        ]);

        if ($user->must_change_password && !in_array($currentRoute, $allowedRoutes)) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Silakan ganti password default Anda sebelum melanjutkan.');
        }

        return $next($request);
    }
}