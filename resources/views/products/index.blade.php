<x-app-layout title="Manajemen Produk">
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Manajemen Produk') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola stok dan informasi produk di semua cabang
                </p>
            </div>
            @can('create products')
            <a href="{{ route('products.create') }}" 
               class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 shadow-md hover:shadow-lg transition">
                <i class="fas fa-plus-circle"></i>
                <span>Tambah Produk</span>
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filter -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" class="flex flex-wrap items-end gap-4">
                    <!-- Search Input -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Cari Produk</label>
                        <div class="relative">
                            <input type="text" name="search" placeholder="Nama produk atau barcode..." 
                                   value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-sm"></i>
                            </div>
                            @if(request('search'))
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-red-500 transition">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Branch Filter (Owner only) -->
                    @if(Auth::user()->hasRole('owner'))
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Cabang</label>
                        <select name="branch_id" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200 py-2.5 px-4 bg-white pr-8">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <!-- Category Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                        <select name="category" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200 py-2.5 px-4 bg-white pr-8">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $c)
                            <option value="{{ $c }}" {{ request('category') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Stock Status Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status Stok</label>
                        <select name="stock_status" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200 py-2.5 px-4 bg-white pr-8">
                            <option value="">Semua Stok</option>
                            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>⚠️ Stok Menipis</option>
                            <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>❌ Stok Habis</option>
                            <option value="good" {{ request('stock_status') == 'good' ? 'selected' : '' }}>✅ Stok Aman</option>
                        </select>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2">
                            <i class="fas fa-search text-sm"></i>
                            <span>Cari</span>
                        </button>
                        
                        @if(request('search') || request('branch_id') || request('category') || request('stock_status'))
                        <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2">
                            <i class="fas fa-redo-alt text-sm"></i>
                            <span>Reset</span>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 border-b">
                            <tr>
                                <th class="px-6 py-3">Barcode</th>
                                <th class="px-6 py-3">Produk</th>
                                <th class="px-6 py-3">Kategori</th>
                                @if(Auth::user()->hasRole('owner'))
                                <th class="px-6 py-3">Cabang</th>
                                @endif
                                <th class="px-6 py-3 text-right">Harga Jual</th>
                                <th class="px-6 py-3 text-right">Harga Beli</th>
                                <th class="px-6 py-3 text-center">Stok</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->barcode }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $product->unit }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">{{ $product->category }}</span>
                                </td>
                                @if(Auth::user()->hasRole('owner'))
                                <td class="px-6 py-4">
                                    <span class="text-xs">{{ $product->branch->name ?? '-' }}</span>
                                </td>
                                @endif
                                <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right text-gray-500">
                                    Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold {{ $product->stock <= $product->min_stock ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ number_format($product->stock) }}
                                    </span>
                                    <span class="text-xs text-gray-400">/ {{ $product->unit }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($product->stock == 0)
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Habis</span>
                                    @elseif($product->stock <= $product->min_stock)
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Menipis</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Tersedia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                    <!-- Detail - Semua role bisa lihat -->
                                    <a href="{{ route('products.show', $product) }}" 
                                    class="text-blue-600 hover:text-blue-800 transition" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Edit -->
                                    @can('edit products')
                                    <a href="{{ route('products.edit', $product) }}"
                                    class="text-green-600 hover:text-green-800 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    
                                    <!-- Atur Stok - Hanya yang punya permission manage stock -->
                                    @can('manage stock')
                                    <button onclick="openStockModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock }})" 
                                            class="text-purple-600 hover:text-purple-800 transition" title="Atur Stok">
                                        <i class="fas fa-boxes"></i>
                                    </button>
                                    @endcan
                                    
                                    <!-- Riwayat Stok - Hanya yang punya permission view stock -->
                                    @can('view stock')
                                    <a href="{{ route('products.stock-history', $product) }}" 
                                    class="text-orange-600 hover:text-orange-800 transition" title="Riwayat Stok">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @endcan
                                    
                                    <!-- Delete -->
                                    @can('delete products')
                                    <button onclick="confirmDelete({{ $product->id }}, '{{ $product->name }}')" 
                                            class="text-red-600 hover:text-red-800 transition" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ Auth::user()->hasRole('owner') ? '9' : '8' }}" 
                                    class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-box-open text-4xl text-gray-300 mb-3 block"></i>
                                    <p>Belum ada produk</p>
                                    @can('create products')
                                    <a href="{{ route('products.create') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                                        Tambah produk pertama →
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Modal -->
    <div id="stockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800" id="modalProductName"></h3>
                    <button onclick="closeStockModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="stockForm" method="POST" action="">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Perubahan</label>
                            <select name="type" id="stockType" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200" onchange="toggleStockInput()">
                                <option value="in">📥 Stok Masuk</option>
                                <option value="out">📤 Stok Keluar</option>
                                <option value="adjustment">⚙️ Penyesuaian Stok</option>
                            </select>
                        </div>
                        <div id="quantityDiv">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                            <input type="number" name="quantity" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200" min="1" placeholder="Masukkan jumlah">
                        </div>
                        <div id="stockDiv" style="display:none;">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok Baru</label>
                            <input type="number" name="stock" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200" min="0" placeholder="Masukkan stok baru">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea name="note" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200" placeholder="Opsional"></textarea>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="closeStockModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Hapus Produk</h3>
                </div>
                <p class="text-gray-600 mb-4">
                    Apakah Anda yakin ingin menghapus produk <span id="deleteProductName" class="font-semibold"></span>?
                </p>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function openStockModal(id, name, stock) {
        document.getElementById('modalProductName').innerHTML = name + ' <span class="text-sm text-gray-500">(Stok saat ini: ' + stock + ')</span>';
        document.getElementById('stockForm').action = '/products/' + id + '/stock';
        document.getElementById('stockModal').classList.remove('hidden');
        document.getElementById('stockModal').classList.add('flex');
    }
    
    function closeStockModal() {
        document.getElementById('stockModal').classList.add('hidden');
        document.getElementById('stockModal').classList.remove('flex');
        document.getElementById('stockType').value = 'in';
        toggleStockInput();
    }
    
    function toggleStockInput() {
        var type = document.getElementById('stockType').value;
        document.getElementById('quantityDiv').style.display = type == 'adjustment' ? 'none' : 'block';
        document.getElementById('stockDiv').style.display = type == 'adjustment' ? 'block' : 'none';
    }
    
    function confirmDelete(id, name) {
        document.getElementById('deleteProductName').textContent = name;
        document.getElementById('deleteForm').action = '/products/' + id;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }
</script>