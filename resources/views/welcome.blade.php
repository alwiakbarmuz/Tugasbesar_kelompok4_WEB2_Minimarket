<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Minimarket Jayusman') }} - Sistem Manajemen</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900" rel="stylesheet" />

    <link rel="icon" type="image/png" href="{{ asset('images/logo/LogoJM.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/LogoJM.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.8; }
        }
        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 min-h-screen">
    <!-- Background Pattern -->
    <div class="fixed inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>
    
    <div class="relative min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white/10 backdrop-blur-md border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3">
                                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                            </div>
                            <div>
                                <span class="text-white font-bold text-lg">Minimarket Jayusman</span>
                                <span class="text-white/70 text-xs block">Sistem Manajemen</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" 
                               class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="flex-grow flex items-center justify-center px-4 py-12">
            <div class="max-w-6xl mx-auto text-center">
                <!-- Logo/Icon Utama -->   
                <img src="{{ asset('images/logo/LogoJM.png') }}" 
                            alt="Logo" 
                            class="animate-float w-28 h-28 mx-auto mb-4">
                <!-- Title -->
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-4">
                    Minimarket Jayusman
                </h1>
                <p class="text-xl md:text-2xl text-blue-200 mb-6">
                    Sistem Manajemen Terintegrasi
                </p>
                
                <div class="h-1 w-24 mx-auto bg-gradient-to-r from-blue-400 to-indigo-400 rounded-full mb-8"></div>
                
                <!-- Description -->
                <p class="text-lg text-blue-100 max-w-2xl mx-auto mb-12">
                    Kelola seluruh cabang, transaksi, dan stok barang dengan mudah 
                    dari satu platform terpusat. Pantau bisnis Anda kapan saja, di mana saja.
                </p>
                
                <!-- Feature Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto mb-12">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                        <div class="w-12 h-12 bg-blue-500/30 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-store text-white text-xl"></i>
                        </div>
                        <h3 class="text-white font-semibold mb-2">Multi Cabang</h3>
                        <p class="text-blue-200 text-sm">Kelola 5 cabang dari satu dashboard</p>
                    </div>
                    
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                        <div class="w-12 h-12 bg-green-500/30 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <h3 class="text-white font-semibold mb-2">Real-time Monitoring</h3>
                        <p class="text-blue-200 text-sm">Pantau transaksi dan stok secara langsung</p>
                    </div>
                    
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                        <div class="w-12 h-12 bg-purple-500/30 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-alt text-white text-xl"></i>
                        </div>
                        <h3 class="text-white font-semibold mb-2">Laporan Lengkap</h3>
                        <p class="text-blue-200 text-sm">Cetak laporan sesuai kebutuhan</p>
                    </div>
                </div>

                @guest
                <div>
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-8 py-3 bg-white text-blue-900 font-semibold rounded-lg hover:bg-blue-50 transition shadow-lg text-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login ke Sistem
                    </a>
                    <p class="text-blue-200 text-sm mt-4">
                        Hanya untuk karyawan internal Minimarket Jayusman.
                    </p>
                </div>
                @endguest
                
                @auth
                <div>
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-8 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition shadow-lg text-lg">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Buka Dashboard
                    </a>
                </div>
                @endauth
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white/5 backdrop-blur-sm border-t border-white/10 py-6">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-blue-200 text-sm">
                    &copy; {{ date('Y') }} JayusmanMart. All rights reserved.
                </p>
                <p class="text-blue-300 text-xs mt-1">
                    Sistem Manajemen Internal - Minimarket Jayusman
                </p>
            </div>
        </footer>
    </div>
</body>
</html>