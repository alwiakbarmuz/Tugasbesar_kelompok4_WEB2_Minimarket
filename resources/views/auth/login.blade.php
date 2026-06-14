<x-guest-layout>
    {{-- Title untuk halaman --}}
    <x-slot name="title">Login</x-slot>
    
    <!-- Mobile Logo (hanya muncul di mobile) -->
    <div class="text-center md:hidden mb-8">
        <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
            <i class="fas fa-store text-white text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Minimarket Jayusman</h2>
        <p class="text-gray-500 text-sm mt-1">Sistem Manajemen Terintegrasi</p>
    </div>
    
    <!-- Login Card -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
        <div class="p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-key text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Selamat Datang Kembali</h3>
                <p class="text-gray-500 text-sm mt-1">Silakan login untuk melanjutkan</p>
            </div>
            
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('status') }}
                </div>
            @endif
            
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-600 text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif
            
            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email Address -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-gray-400"></i>
                        Alamat Email
                    </label>
                    <div class="relative">
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username"
                               placeholder="email@youremail.com"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 @error('email') border-red-500 @enderror">
                        @error('email')
                            <div class="absolute right-3 top-3 text-red-500">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                        @enderror
                    </div>
                </div>
                
                <!-- Password -->
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-gray-400"></i>
                        Password
                    </label>
                    <div class="relative">
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               placeholder="Masukkan password Anda"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 @error('password') border-red-500 @enderror">
                        <button type="button" 
                                onclick="togglePassword()"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 transition">
                            <i id="password-toggle" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="remember" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                    
                    <a href="{{ route('password.request') }}" 
                       class="text-sm text-blue-600 hover:text-blue-800 transition">
                        Lupa password?
                    </a>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </button>
            </form>

            <!-- Tombol Kembali ke Beranda / Welcome Page -->
            <div class="mt-4">
                <a href="{{ url('/') }}" 
                   class="w-full flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left text-sm"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
            
            <!-- Footer Info -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-center text-xs text-gray-500">
                    <i class="fas fa-shield-alt mr-1 text-green-500"></i>
                    <span>Sistem aman & terenkripsi</span>
                    <span class="mx-2">•</span>
                    <i class="fas fa-clock mr-1 text-blue-500"></i>
                    <span>Akses 24/7</span>
                </div>
                <p class="text-center text-xs text-gray-400 mt-3">
                    <i class="fas fa-lock mr-1"></i>
                    Hanya untuk karyawan internal.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Version -->
    <div class="text-center mt-6 text-xs text-gray-400">
        <i class="fas fa-code-branch mr-1"></i>
        Version 1.0.0
    </div>

    <!-- Toggle Password Script -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</x-guest-layout>