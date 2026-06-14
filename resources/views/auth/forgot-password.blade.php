<x-guest-layout>
    <x-slot name="title">Lupa Password</x-slot>
    
    <!-- Mobile Logo -->
    <div class="text-center md:hidden mb-8">
        <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
            <i class="fas fa-key text-white text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Minimarket Jayusman</h2>
        <p class="text-gray-500 text-sm mt-1">Reset Password</p>
    </div>
    
    <!-- Forgot Password Card -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
        <div class="p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-key text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Lupa Password?</h3>
                <p class="text-gray-500 text-sm mt-1">
                    Tenang! Masukkan email Anda dan kami akan mengirimkan link reset password.
                </p>
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
            
            <!-- Forgot Password Form -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                
                <!-- Email Address -->
                <div class="mb-6">
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
                               placeholder="nama@perusahaan.com"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200">
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="flex flex-col space-y-3">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition duration-200 shadow-md">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Kirim Link Reset
                    </button>
                    
                    <a href="{{ route('login') }}" 
                       class="text-center text-sm text-gray-600 hover:text-gray-800 transition">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Version -->
    <div class="text-center mt-6 text-xs text-gray-400">
        <i class="fas fa-code-branch mr-1"></i>
        Version 1.0.0
    </div>
</x-guest-layout>