<section>
    <form method="post" action="{{ route('profile.change-password') }}" class="space-y-5" id="passwordForm">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-2 text-green-500"></i>
                Password Saat Ini
            </label>
            <div class="relative">
                <input id="update_password_current_password" 
                       name="current_password" 
                       type="password" 
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition duration-200 @error('current_password', 'updatePassword') border-red-500 @enderror" 
                       autocomplete="current-password"
                       placeholder="Masukkan password saat ini">
                <button type="button" 
                        onclick="togglePassword('update_password_current_password', 'toggle-current')"
                        class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 transition">
                    <i id="toggle-current" class="fas fa-eye text-sm"></i>
                </button>
            </div>
            <x-input-error class="mt-1 text-xs" :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <!-- New Password -->
        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-key mr-2 text-green-500"></i>
                Password Baru
            </label>
            <div class="relative">
                <input id="update_password_password" 
                       name="password" 
                       type="password" 
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition duration-200 @error('password', 'updatePassword') border-red-500 @enderror" 
                       autocomplete="new-password"
                       placeholder="Minimal 8 karakter">
                <button type="button" 
                        onclick="togglePassword('update_password_password', 'toggle-new')"
                        class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 transition">
                    <i id="toggle-new" class="fas fa-eye text-sm"></i>
                </button>
            </div>
            <x-input-error class="mt-1 text-xs" :messages="$errors->updatePassword->get('password')" />
            <p class="text-xs text-gray-400 mt-1">
                <i class="fas fa-info-circle mr-1"></i>
                Gunakan password yang kuat dengan kombinasi huruf, angka, dan simbol
            </p>
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-check-circle mr-2 text-green-500"></i>
                Konfirmasi Password Baru
            </label>
            <div class="relative">
                <input id="update_password_password_confirmation" 
                       name="password_confirmation" 
                       type="password" 
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition duration-200" 
                       autocomplete="new-password"
                       placeholder="Ketik ulang password baru">
                <button type="button" 
                        onclick="togglePassword('update_password_password_confirmation', 'toggle-confirm')"
                        class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 transition">
                    <i id="toggle-confirm" class="fas fa-eye text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-2">
            <div class="flex items-center space-x-3">
                <button type="submit" 
                        id="submitPasswordBtn"
                        class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white font-semibold px-6 py-2 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-key mr-2"></i>
                    Update Password
                </button>
                
                @if (session('status') === 'password-updated')
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                         class="flex items-center text-green-600 text-sm bg-green-50 px-3 py-1.5 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>
                        Password berhasil diupdate!
                    </div>
                @endif
            </div>
        </div>
    </form>
</section>

<script>
    function togglePassword(inputId, toggleId) {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(toggleId);
        
        if (input.type === 'password') {
            input.type = 'text';
            toggle.classList.remove('fa-eye');
            toggle.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            toggle.classList.remove('fa-eye-slash');
            toggle.classList.add('fa-eye');
        }
    }
    
    document.getElementById('passwordForm')?.addEventListener('submit', function(e) {

        const btn = document.getElementById('submitPasswordBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
        btn.disabled = true;

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 5000);
    });
</script>