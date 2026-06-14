<x-app-layout title="Audit Produk">
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Produk Terhapus') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Riwayat produk yang dihapus
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Kembali ke Produk
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <!-- Search -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cari Produk</label>
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="search" placeholder="Nama produk atau barcode..." 
                                       value="{{ request('search') }}"
                                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm">
                                @if(request('search'))
                                <a href="{{ route('audit.products') }}" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Branch Filter -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cabang</label>
                            <select name="branch_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 py-2 px-3 text-sm bg-white">
                                <option value="">Semua Cabang</option>
                                @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="flex gap-2 items-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            @if(request('search') || request('branch_id'))
                            <a href="{{ route('audit.products') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                                <i class="fas fa-times"></i> Reset
                            </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-red-100 text-sm">Total Produk Terhapus</p>
                            <p class="text-2xl font-bold">{{ number_format($products->total()) }}</p>
                        </div>
                        <i class="fas fa-trash-alt text-3xl text-red-200"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-orange-100 text-sm">Nilai Stok Terhapus</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($products->sum(function($p) { return $p->stock * $p->purchase_price; }), 0, ',', '.') }}</p>
                        </div>
                        <i class="fas fa-money-bill-wave text-3xl text-orange-200"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-purple-100 text-sm">Rata-rata Harga</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($products->avg('price') ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <i class="fas fa-tag text-3xl text-purple-200"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-blue-100 text-sm">Total Kategori</p>
                            <p class="text-2xl font-bold">{{ number_format($products->pluck('category')->unique()->count()) }}</p>
                        </div>
                        <i class="fas fa-folder-open text-3xl text-blue-200"></i>
                    </div>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left w-[10%]">Barcode</th>
                                <th class="px-4 py-3 text-left w-[15%]">Produk</th>
                                <th class="px-4 py-3 text-left w-[10%]">Kategori</th>
                                <th class="px-4 py-3 text-left w-[10%]">Cabang</th>
                                <th class="px-4 py-3 text-center w-[5%]">Stok</th>
                                <th class="px-4 py-3 text-right w-[10%]">Harga Jual</th>
                                <th class="px-4 py-3 text-right w-[10%]">Harga Beli</th>
                                <th class="px-4 py-3 text-left w-[12%]">Tgl Dihapus</th>
                                <th class="px-4 py-3 text-left w-[10%]">Dihapus Oleh</th>
                                <th class="px-4 py-3 text-center w-[8%]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->barcode }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $product->unit }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">{{ $product->category }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs">{{ $product->branch->name ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-bold text-red-600">{{ number_format($product->stock) }}</span>
                                    <span class="text-xs text-gray-400">/ {{ $product->unit }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-500">
                                    Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-red-600">
                                    {{ $product->deleted_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $product->deletedBy->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <button onclick="restoreProduct({{ $product->id }}, '{{ addslashes($product->name) }}')" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-green-600 hover:bg-green-100 transition"
                                                title="Kembalikan Produk">
                                            <i class="fas fa-undo-alt text-sm"></i>
                                        </button>
                                        <button onclick="forceDeleteProduct({{ $product->id }}, '{{ addslashes($product->name) }}')" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-100 transition"
                                                title="Hapus Permanen">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="px-4 py-12 text-center text-gray-500">
                                    <i class="fas fa-box-open text-4xl text-gray-300 mb-3 block"></i>
                                    <p>Belum ada produk yang dihapus</p>
                                    <p class="text-xs mt-1">Produk yang dihapus akan muncul di sini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
            
            <!-- Info Note -->
            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start gap-2">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <div class="text-xs text-blue-700">
                        <p class="font-medium">Informasi Soft Delete:</p>
                        <p>- Produk yang <strong>belum pernah bertransaksi</strong> akan langsung dihapus permanen (tidak muncul di sini)</p>
                        <p>- Produk yang <strong>sudah pernah bertransaksi</strong> hanya disembunyikan (soft delete) dan dapat dikembalikan</p>
                        <p>- Menghapus permanen akan menghilangkan data produk selamanya dan tidak dapat dikembalikan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function restoreProduct(id, name) {
        Swal.fire({
            title: 'Kembalikan Produk?',
            html: `Apakah Anda yakin ingin mengembalikan produk <strong>${name}</strong>?<br><br>
                   <span class="text-green-600">✅ Stok akan kembali seperti semula.</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Kembalikan!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch(`/audit/products/${id}/restore`, { 
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    } 
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Produk berhasil dikembalikan',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.error || 'Gagal mengembalikan produk',
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
                        confirmButtonColor: '#d33'
                    });
                });
            }
        });
    }
    
    function forceDeleteProduct(id, name) {
        Swal.fire({
            title: 'Hapus Permanen!',
            html: `Apakah Anda yakin ingin menghapus <strong>PERMANEN</strong> produk <strong>${name}</strong>?<br><br>
                   <span class="text-red-600 font-bold">⚠️ TINDAKAN INI TIDAK DAPAT DIBATALKAN!</span><br>
                   Data akan hilang selamanya dari sistem.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus Permanen!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menghapus data secara permanen',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch(`/audit/products/${id}/force-delete`, { 
                    method: 'DELETE', 
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    } 
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Produk berhasil dihapus permanen',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.error || 'Gagal menghapus produk',
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
                        confirmButtonColor: '#d33'
                    });
                });
            }
        });
    }
</script>