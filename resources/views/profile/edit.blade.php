<x-app-layout title="Kelola Profil">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Profil Saya') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola informasi profil dan keamanan akun Anda
                </p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500" id="profile-date"></div>
                <div class="text-xs text-gray-400" id="profile-time"></div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(Auth::user()->must_change_password)
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-yellow-800">⚠️ Peringatan Keamanan!</p>
                        <p class="text-sm text-yellow-700 mt-1">
                            Anda menggunakan password default. <strong>Silakan ganti password Anda sekarang juga</strong> untuk keamanan akun.
                        </p>
                        <p class="text-xs text-yellow-600 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Password default: <code class="bg-yellow-100 px-1 rounded">password123</code>
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- User Info Card -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg mb-6 overflow-hidden">
                <div class="px-6 py-8">
                    <div class="flex items-center space-x-4">
                        <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-user-circle text-white text-5xl"></i>
                        </div>
                        <div class="text-white">
                            <h3 class="text-2xl font-bold">{{ Auth::user()->name }}</h3>
                            <p class="text-blue-100">{{ Auth::user()->email }}</p>
                            <div class="mt-2">
                                <span class="px-2 py-1 bg-white/20 rounded-lg text-xs">
                                    <i class="fas fa-store mr-1"></i>
                                    {{ Auth::user()->branch->name ?? 'Owner' }}
                                </span>
                                <span class="px-2 py-1 bg-white/20 rounded-lg text-xs ml-2">
                                    <i class="fas fa-user-tag mr-1"></i>
                                    {{ ucfirst(Auth::user()->roles->first()->name ?? 'User') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Profile Information -->
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-300">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Informasi Profil</h3>
                                    <p class="text-xs text-gray-500">Update informasi akun Anda</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Delete Account -->
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-300">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-red-50 to-white px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-trash-alt text-red-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Hapus Akun</h3>
                                    <p class="text-xs text-gray-500">Hapus akun secara permanen</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <!-- Update Password -->
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-300">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-key text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Keamanan Akun</h3>
                                    <p class="text-xs text-gray-500">Ganti password Anda</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Activity Log (Optional) -->
                    <div class="mt-6 bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-300">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-history text-purple-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Aktivitas Terakhir</h3>
                                    <p class="text-xs text-gray-500">Riwayat login Anda</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-sign-in-alt text-green-500 text-sm"></i>
                                        <span class="text-sm text-gray-600">Login terakhir</span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ Auth::user()->last_login_at ?? 'Belum tercatat' }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-calendar-alt text-blue-500 text-sm"></i>
                                        <span class="text-sm text-gray-600">Akun dibuat</span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ Auth::user()->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-shield-alt text-purple-500 text-sm"></i>
                                        <span class="text-sm text-gray-600">Role</span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ ucfirst(Auth::user()->roles->first()->name ?? 'User') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Real-time clock for profile page
    function updateDateTime() {
        const now = new Date();
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const dateElement = document.getElementById('profile-date');
        const timeElement = document.getElementById('profile-time');
        
        if (dateElement) {
            dateElement.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        }
        if (timeElement) {
            timeElement.textContent = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')} WIB`;
        }
    }
    
    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>