<x-app-layout title="Informasi Produk">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Detail Produk') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Informasi lengkap produk
                </p>
            </div>
            <div class="flex space-x-3">
                @can('edit products')
                <a href="{{ route('products.edit', $product) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                @endcan
                <a href="{{ route('products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Product Info Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-box text-white text-2xl"></i>
                                <div>
                                    <h3 class="text-white font-bold text-xl">{{ $product->name }}</h3>
                                    <p class="text-blue-100 text-sm">Barcode: {{ $product->barcode }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">Kategori</p>
                                    <p class="font-medium">{{ $product->category }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Satuan</p>
                                    <p class="font-medium">{{ $product->unit }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Cabang</p>
                                    <p class="font-medium">{{ $product->branch->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Kota</p>
                                    <p class="font-medium">{{ $product->branch->city ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stock Status Card -->
                <div>
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 text-center">
                            <div class="text-5xl mb-3">
                                @if($product->stock == 0)
                                    <i class="fas fa-ban text-red-500"></i>
                                @elseif($product->stock <= $product->min_stock)
                                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                @else
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @endif
                            </div>
                            <div class="text-3xl font-bold {{ $product->stock <= $product->min_stock ? 'text-red-600' : 'text-gray-800' }}">
                                {{ number_format($product->stock) }}
                            </div>
                            <p class="text-sm text-gray-500">Stok Tersedia ({{ $product->unit }})</p>
                            <div class="mt-3">
                                @if($product->stock == 0)
                                    <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800">Stok Habis</span>
                                @elseif($product->stock <= $product->min_stock)
                                    <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">Stok Menipis</span>
                                @else
                                    <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Stok Aman</span>
                                @endif
                            </div>
                            <div class="mt-4 pt-3 border-t">
                                <p class="text-xs text-gray-500">Stok Minimal</p>
                                <p class="font-semibold">{{ $product->min_stock }} {{ $product->unit }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pricing Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-green-100 text-sm">Harga Jual</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="text-green-200 text-xs mt-1">Per {{ $product->unit }}</p>
                        </div>
                        <i class="fas fa-tag text-4xl text-green-200"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-orange-100 text-sm">Harga Beli</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</p>
                            <p class="text-orange-200 text-xs mt-1">Per {{ $product->unit }}</p>
                        </div>
                        <i class="fas fa-money-bill-wave text-4xl text-orange-200"></i>
                    </div>
                </div>
            </div>
            
            <!-- Profit Margin -->
            <div class="bg-white rounded-xl shadow-sm mt-6 p-6">
                <h3 class="font-semibold text-gray-800 mb-3">Margin Keuntungan</h3>
                @php
                    $profit = $product->price - $product->purchase_price;
                    $margin = $product->purchase_price > 0 ? ($profit / $product->purchase_price) * 100 : 0;
                @endphp
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Laba per unit</p>
                        <p class="text-xl font-bold text-green-600">Rp {{ number_format($profit, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Persentase Margin</p>
                        <p class="text-xl font-bold text-blue-600">{{ number_format($margin, 1) }}%</p>
                    </div>
                    <div class="w-32">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 rounded-full h-2" style="width: {{ min($margin, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex gap-3 mt-6">
                @can('manage stock')
                <button onclick="openStockModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock }})" 
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-boxes"></i>
                    <span>Atur Stok</span>
                </button>
                @endcan

                @can('view stock')
                <a href="{{ route('products.stock-history', $product) }}" 
                class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-history"></i>
                    <span>Riwayat Stok</span>
                </a>
                @endcan
            </div>

            @if(!Auth::user()->can('manage stock') && !Auth::user()->can('view stock'))
            <div class="text-center py-6 bg-gray-50 rounded-lg">
                <i class="fas fa-lock text-gray-400 text-4xl mb-2"></i>
                <p class="text-gray-500 text-sm">Anda tidak memiliki akses untuk mengelola stok produk ini</p>
                <p class="text-xs text-gray-400 mt-1">Silakan hubungi manager atau warehouse untuk perubahan stok</p>
            </div>
            @endif
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
                            <select name="type" id="stockType" class="w-full rounded-lg border-gray-300" onchange="toggleStockInput()">
                                <option value="in">📥 Stok Masuk</option>
                                <option value="out">📤 Stok Keluar</option>
                                <option value="adjustment">⚙️ Penyesuaian Stok</option>
                            </select>
                        </div>
                        <div id="quantityDiv">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                            <input type="number" name="quantity" class="w-full rounded-lg border-gray-300" min="1">
                        </div>
                        <div id="stockDiv" style="display:none;">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok Baru</label>
                            <input type="number" name="stock" class="w-full rounded-lg border-gray-300" min="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea name="note" rows="2" class="w-full rounded-lg border-gray-300" placeholder="Opsional"></textarea>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="closeStockModal()" class="px-4 py-2 bg-gray-100 rounded-lg">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function openStockModal(id, name, stock) {
        document.getElementById('modalProductName').innerHTML = name + ' <span class="text-sm text-gray-500">(Stok: ' + stock + ')</span>';
        document.getElementById('stockForm').action = '/products/' + id + '/stock';
        document.getElementById('stockModal').classList.remove('hidden');
        document.getElementById('stockModal').classList.add('flex');
    }
    
    function closeStockModal() {
        document.getElementById('stockModal').classList.add('hidden');
        document.getElementById('stockModal').classList.remove('flex');
    }
    
    function toggleStockInput() {
        var type = document.getElementById('stockType').value;
        document.getElementById('quantityDiv').style.display = type == 'adjustment' ? 'none' : 'block';
        document.getElementById('stockDiv').style.display = type == 'adjustment' ? 'block' : 'none';
    }
</script>