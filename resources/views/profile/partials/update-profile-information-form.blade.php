<section>
    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <!-- Name Field -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-user mr-2 text-blue-500"></i>
                Nama Lengkap
            </label>
            <div class="relative">
                <input id="name" 
                       name="name" 
                       type="text" 
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 @error('name') border-red-500 @enderror" 
                       value="{{ old('name', $user->name) }}" 
                       required 
                       autofocus 
                       autocomplete="name"
                       placeholder="Masukkan nama lengkap">
                <div class="absolute right-3 top-3 text-gray-400">
                    <i class="fas fa-pen text-sm"></i>
                </div>
            </div>
            <x-input-error class="mt-1 text-xs" :messages="$errors->get('name')" />
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-envelope mr-2 text-blue-500"></i>
                Alamat Email
            </label>
            <div class="relative">
                <input id="email" 
                       name="email" 
                       type="email" 
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 @error('email') border-red-500 @enderror" 
                       value="{{ old('email', $user->email) }}" 
                       required 
                       autocomplete="username"
                       placeholder="email@perusahaan.com">
                <div class="absolute right-3 top-3 text-gray-400">
                    <i class="fas fa-envelope text-sm"></i>
                </div>
            </div>
            <x-input-error class="mt-1 text-xs" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Email Anda belum diverifikasi.
                        <button form="send-verification" class="underline text-yellow-700 hover:text-yellow-900 font-medium">
                            Kirim ulang email verifikasi
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>
                            Link verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-2">
            <div class="flex items-center space-x-3">
                <button type="submit" 
                        class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold px-6 py-2 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
                
                @if (session('status') === 'profile-updated')
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                         class="flex items-center text-green-600 text-sm bg-green-50 px-3 py-1.5 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>
                        Profil berhasil diperbarui!
                    </div>
                @endif
            </div>
        </div>
    </form>
</section>