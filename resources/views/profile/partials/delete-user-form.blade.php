<section class="space-y-6">
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-red-800">
                    <span class="font-semibold">Peringatan!</span> Menghapus akun bersifat permanen. 
                    Semua data yang terkait dengan akun ini akan hilang selamanya.
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition duration-200 flex items-center space-x-2 shadow-sm hover:shadow-md">
            <i class="fas fa-trash-alt text-sm"></i>
            <span>Hapus Akun Saya</span>
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ __('Hapus Akun Permanen?') }}
                </h2>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                {{ __('Apakah Anda yakin ingin menghapus akun ini? Semua data Anda akan dihapus secara permanen. Silakan masukkan password Anda untuk konfirmasi.') }}
            </p>

            <form method="post" action="{{ route('profile.destroy') }}" class="mt-4">
                @csrf
                @method('delete')

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-red-500"></i>
                        Password Konfirmasi
                    </label>
                    <div class="relative">
                        <input id="delete_password" 
                               name="password" 
                               type="password" 
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition duration-200 @error('password', 'userDeletion') border-red-500 @enderror" 
                               placeholder="Masukkan password Anda">
                        <button type="button" 
                                onclick="toggleDeletePassword()"
                                class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 transition">
                            <i id="toggle-delete" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1 text-xs" />
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" 
                            x-on:click="$dispatch('close')"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        {{ __('Batal') }}
                    </button>
                    
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition duration-200 flex items-center space-x-2">
                        <i class="fas fa-trash-alt"></i>
                        <span>{{ __('Hapus Akun') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</section>

<script>
    function toggleDeletePassword() {
        const passwordInput = document.getElementById('delete_password');
        const toggleIcon = document.getElementById('toggle-delete');
        
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