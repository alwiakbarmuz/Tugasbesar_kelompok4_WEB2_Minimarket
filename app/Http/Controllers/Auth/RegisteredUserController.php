<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        abort(404, 'Registrasi tidak tersedia. Hubungi administrator untuk membuat akun.');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): Response
    {
        abort(404, 'Registrasi tidak tersedia. Hubungi administrator untuk membuat akun.');
    }
}