<x-app-layout title="Manajemen Cabang">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Manajemen Cabang') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola semua cabang minimarket Jayusman
                </p>
            </div>
            @can('create branches')
                @if($isFull ?? false)
                    <button disabled
                            class="bg-gray-400 cursor-not-allowed text-white px-4 py-2 rounded-lg flex items-center space-x-2 shadow-md"
                            title="Slot cabang sudah penuh (maksimal {{ $maxBranches ?? 5 }} cabang)">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Cabang</span>
                    </button>
                @else
                    <a href="{{ route('branches.create') }}" 
                       class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 shadow-md hover:shadow-lg transition">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Cabang</span>
                    </a>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Info Slot Cabang -->
            @if(Auth::user()->hasRole('owner'))
            <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-store text-blue-500"></i>
                        <span class="text-sm text-gray-600">Slot Cabang:</span>
                        <div class="flex items-center space-x-1">
                            @for($i = 1; $i <= ($maxBranches ?? 5); $i++)
                                @if($i <= ($currentBranchesCount ?? 0))
                                    <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center" title="Terisi">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                @else
                                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center" title="Kosong">
                                        <i class="fas fa-minus text-gray-400 text-xs"></i>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>
                    <div class="text-sm">
                        <span class="font-semibold text-blue-600">{{ $currentBranchesCount ?? 0 }}</span>
                        <span class="text-gray-500"> dari </span>
                        <span class="font-semibold">{{ $maxBranches ?? 5 }}</span>
                        <span class="text-gray-500"> cabang terpakai</span>
                        @if(($remainingSlots ?? 0) > 0)
                            <span class="text-green-600 ml-2">(Sisa {{ $remainingSlots ?? 0 }} slot)</span>
                        @else
                            <span class="text-red-600 ml-2">(Slot penuh)</span>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Search & Filter -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Cari Cabang</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" name="search" placeholder="Nama cabang atau kode..." 
                                value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                            @if(request('search'))
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <a href="{{ route('branches.index') }}" class="text-gray-400 hover:text-red-500 transition">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status Cabang</label>
                        <select name="status" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200 py-2.5 px-4 pr-8 bg-white">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>❌ Nonaktif</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2">
                            <i class="fas fa-search text-sm"></i>
                            <span>Cari</span>
                        </button>
                        
                        @if(request('search') || request('status'))
                        <a href="{{ route('branches.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2">
                            <i class="fas fa-redo-alt text-sm"></i>
                            <span>Reset</span>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Branches Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($branches as $branch)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col h-full">
                    
                    <!-- Header Card - Fixed -->
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-3 flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-store text-white text-xl"></i>
                            <span class="text-white font-semibold">{{ $branch->code }}</span>
                        </div>
                        @if($branch->is_active)
                        <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full flex-shrink-0">
                            <i class="fas fa-circle mr-1 text-xs"></i> Aktif
                        </span>
                        @else
                        <span class="px-2 py-1 bg-gray-500 text-white text-xs rounded-full flex-shrink-0">
                            <i class="fas fa-circle mr-1 text-xs"></i> Nonaktif
                        </span>
                        @endif
                    </div>
                    
                    <!-- Body Card - Flex Grow to fill space -->
                    <div class="p-4 flex-1 flex flex-col">
                        <!-- Nama Cabang -->
                        <h3 class="font-bold text-lg text-gray-800 mb-2 break-words" title="{{ $branch->name }}">
                            {{ $branch->name }}
                        </h3>
                        
                        <!-- Alamat -->
                        <div class="space-y-2 text-sm text-gray-600 flex-1">
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-map-marker-alt text-blue-500 mt-0.5 flex-shrink-0"></i>
                                <span class="break-words" title="{{ $branch->address }}, {{ $branch->city }}">
                                    {{ $branch->address }}, {{ $branch->city }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-phone text-blue-500 flex-shrink-0"></i>
                                <span class="break-words">
                                    {{ $branch->phone }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Statistics - Tetap di bawah -->
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <div class="grid grid-cols-3 gap-2">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Produk</p>
                                    <p class="font-bold text-gray-800">{{ number_format($stats[$branch->id]['products_count'] ?? 0) }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Karyawan</p>
                                    <p class="font-bold text-gray-800">{{ number_format($stats[$branch->id]['employees_count'] ?? 0) }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Transaksi</p>
                                    <p class="font-bold text-gray-800">{{ number_format($stats[$branch->id]['transactions_total'] ?? 0) }}</p>
                                </div>
                            </div>
                            
                            <div class="mt-3 pt-2 border-t border-gray-100">
                                <div class="flex justify-between items-center">
                                    <p class="text-xs text-gray-500">Total Pendapatan</p>
                                    <p class="text-sm font-bold text-green-600">
                                        Rp {{ number_format($stats[$branch->id]['revenue_total'] ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer Card - Tetap di paling bawah -->
                    <div class="bg-gray-50 px-4 py-3 flex justify-end items-center mt-auto">
                        <div class="flex space-x-2 flex-shrink-0">
                            <a href="{{ route('branches.show', $branch) }}" 
                            class="text-blue-600 hover:text-blue-800 transition p-1.5 rounded-lg hover:bg-gray-200" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('edit branches')
                            <a href="{{ route('branches.edit', $branch) }}" 
                            class="text-green-600 hover:text-green-800 transition p-1.5 rounded-lg hover:bg-gray-200" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            
                            <button id="menu-btn-{{ $branch->id }}" 
                                    onclick="openDropdown({{ $branch->id }}, '{{ addslashes($branch->name) }}', {{ $branch->is_active ? 'true' : 'false' }}, event)"
                                    class="text-gray-500 hover:text-gray-700 transition p-1.5 rounded-lg hover:bg-gray-200"
                                    title="Menu Lainnya">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12 bg-white rounded-xl">
                    <i class="fas fa-store-slash text-5xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada cabang</p>
                    @can('create branches')
                    <a href="{{ route('branches.create') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                        Tambah cabang pertama →
                    </a>
                    @endcan
                </div>
                @endforelse
            </div>
            
            <div class="mt-6">
                {{ $branches->links() }}
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Hapus Cabang</h3>
                </div>
                <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menghapus cabang <span id="deleteBranchName" class="font-semibold"></span>?</p>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Reset Password Confirmation Modal -->
    <div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Reset Password Massal</h3>
                </div>
                <p class="text-gray-600 mb-3">
                    Apakah Anda yakin ingin mereset <strong>SEMUA password</strong> di cabang 
                    <span id="resetBranchName" class="font-semibold text-orange-600"></span>?
                </p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4 rounded">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-500 mt-0.5 mr-2"></i>
                        <div class="text-sm text-yellow-700">
                            <p class="font-medium">Dampak Reset Password:</p>
                            <ul class="list-disc list-inside text-xs mt-1 space-y-1">
                                <li>Semua karyawan akan mendapatkan password default: <strong>password123</strong></li>
                                <li>Semua karyawan akan diwajibkan mengganti password saat login berikutnya</li>
                                <li>Password lama akan tidak dapat digunakan lagi</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <form id="resetPasswordForm" method="POST" action="">
                    @csrf
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeResetModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-key"></i> Ya, Reset Semua Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <form id="toggleStatusForm" method="POST" action="">
        @csrf
    </form>
</x-app-layout>

<script>
    let activeDropdown = null;
    
    function openDropdown(branchId, branchName, isActive, event) {
        event.stopPropagation();
        
        // Tutup dropdown yang sedang terbuka
        if (activeDropdown) {
            activeDropdown.remove();
            activeDropdown = null;
        }
        
        // Dapatkan posisi tombol
        const button = document.getElementById(`menu-btn-${branchId}`);
        const rect = button.getBoundingClientRect();
        
        // Buat dropdown element
        const dropdown = document.createElement('div');
        dropdown.id = `dropdown-${branchId}`;
        dropdown.className = 'bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden';
        dropdown.style.position = 'fixed';
        dropdown.style.top = `${rect.bottom + 5}px`;
        dropdown.style.right = `${window.innerWidth - rect.right}px`;
        dropdown.style.zIndex = '99999';
        dropdown.style.minWidth = '200px';
        
        dropdown.innerHTML = `
            <button onclick="window.confirmResetPasswords(${branchId}, '${branchName}')" 
                    class="w-full text-left px-4 py-2.5 text-sm text-orange-600 hover:bg-orange-50 transition flex items-center gap-3 whitespace-nowrap">
                <i class="fas fa-key w-4"></i> Reset Semua Password
            </button>
            <div class="border-t border-gray-100"></div>
            <button onclick="window.toggleBranchStatus(${branchId}, ${isActive})" 
                    class="w-full text-left px-4 py-2.5 text-sm text-yellow-600 hover:bg-yellow-50 transition flex items-center gap-3 whitespace-nowrap">
                <i class="fas fa-power-off w-4"></i> ${isActive ? 'Nonaktifkan Cabang' : 'Aktifkan Cabang'}
            </button>
            <div class="border-t border-gray-100"></div>
            <button onclick="window.confirmDeleteBranch(${branchId}, '${branchName}')" 
                    class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-3 whitespace-nowrap">
                <i class="fas fa-trash-alt w-4"></i> Hapus Cabang
            </button>
        `;
        
        document.body.appendChild(dropdown);
        activeDropdown = dropdown;
        
        // Tutup dropdown saat klik di luar
        setTimeout(() => {
            const closeDropdown = (e) => {
                if (!dropdown.contains(e.target) && e.target !== button) {
                    dropdown.remove();
                    activeDropdown = null;
                    document.removeEventListener('click', closeDropdown);
                }
            };
            document.addEventListener('click', closeDropdown);
        }, 100);
    }
    
    // Fungsi global untuk digunakan di dropdown
    window.confirmResetPasswords = function(branchId, branchName) {
        document.getElementById('resetBranchName').textContent = branchName;
        document.getElementById('resetPasswordForm').action = `/branches/${branchId}/reset-passwords`;
        document.getElementById('resetPasswordModal').classList.remove('hidden');
        document.getElementById('resetPasswordModal').classList.add('flex');
        if (activeDropdown) {
            activeDropdown.remove();
            activeDropdown = null;
        }
    }
    
    window.toggleBranchStatus = function(branchId, currentStatus) {
        const form = document.getElementById('toggleStatusForm');
        form.action = `/branches/${branchId}/toggle-status`;
        form.submit();
        if (activeDropdown) {
            activeDropdown.remove();
            activeDropdown = null;
        }
    }
    
    window.confirmDeleteBranch = function(branchId, branchName) {
        document.getElementById('deleteBranchName').textContent = branchName;
        document.getElementById('deleteForm').action = `/branches/${branchId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
        if (activeDropdown) {
            activeDropdown.remove();
            activeDropdown = null;
        }
    }
    
    function confirmDelete(id, name) {
        document.getElementById('deleteBranchName').textContent = name;
        document.getElementById('deleteForm').action = `/branches/${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }
    
    function toggleStatus(id, currentStatus) {
        const form = document.getElementById('toggleStatusForm');
        form.action = `/branches/${id}/toggle-status`;
        form.submit();
    }
    
    function confirmResetPasswordsModal(branchId, branchName) {
        document.getElementById('resetBranchName').textContent = branchName;
        document.getElementById('resetPasswordForm').action = `/branches/${branchId}/reset-passwords`;
        document.getElementById('resetPasswordModal').classList.remove('hidden');
        document.getElementById('resetPasswordModal').classList.add('flex');
    }
    
    function closeResetModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
        document.getElementById('resetPasswordModal').classList.remove('flex');
    }
</script>