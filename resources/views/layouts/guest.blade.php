<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Minimarket Jayusman') }} - {{ $title ?? 'Authentication' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo/LogoJM.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/LogoJM.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .bg-pattern {
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.25);
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Left Side - Branding Area (Tetap di Layout) -->
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 relative overflow-hidden">
            <div class="absolute inset-0 bg-pattern opacity-20"></div>
            
            <div class="absolute top-20 -left-20 w-64 h-64 bg-blue-500 rounded-full opacity-20 blur-3xl animate-float"></div>
            <div class="absolute bottom-20 -right-20 w-80 h-80 bg-indigo-500 rounded-full opacity-20 blur-3xl" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-400 rounded-full opacity-10 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col justify-center items-center text-white p-12 w-full">
                <div class="text-center max-w-md mx-auto">
                    {{-- <div class="animate-float mb-8">
                        <div class="w-24 h-24 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto border border-white/20">
                            <i class="fas fa-store text-white text-4xl"></i>
                        </div>
                    </div> --}}
                    
                    <div class="text-center mb-8">
                        <img src="{{ asset('images/logo/LogoJM.png') }}" 
                            alt="Logo" 
                            class="animate-float w-24 h-24 mx-auto mb-4">
                        <h1 class="text-4xl font-bold mb-4">Minimarket Jayusman</h1>
                        <p class="text-blue-200 text-lg mb-8">Sistem Manajemen Terintegrasi</p>
                    </div>
                    
                    <div class="space-y-4 text-blue-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-store text-sm"></i>
                            </div>
                            <span>Kelola Multi Cabang</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-sm"></i>
                            </div>
                            <span>Monitoring Real-time</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-sm"></i>
                            </div>
                            <span>Laporan Lengkap</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Dynamic Content (Slot dari child view) -->
        <div class="flex-1 flex items-center justify-center p-6 md:p-12 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="w-full max-w-md fade-in-up">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>