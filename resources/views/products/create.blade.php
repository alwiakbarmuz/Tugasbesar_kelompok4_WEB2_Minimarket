<x-app-layout title="Tambah Produk">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Tambah Produk Baru') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Tambahkan produk baru ke katalog minimarket
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <form method="POST" action="{{ route('products.store') }}" class="p-6 space-y-6">
                    @csrf
                    
                    <!-- Barcode & Name Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-barcode mr-2 text-blue-500"></i>
                                Barcode <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="barcode" value="{{ old('barcode') }}" required
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                       placeholder="Scan atau masukkan barcode">
                                <button type="button" onclick="generateBarcode()" 
                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-blue-600 hover:text-blue-800 text-sm">
                                    Generate
                                </button>
                            </div>
                            @error('barcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-2 text-blue-500"></i>
                                Nama Produk <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                   placeholder="Contoh: Indomie Goreng">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <!-- Category & Unit Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-folder mr-2 text-blue-500"></i>
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select name="category" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                <option value="">Pilih Kategori</option>
                                <option value="Makanan Ringan" {{ old('category') == 'Makanan Ringan' ? 'selected' : '' }}>🍪 Makanan Ringan</option>
                                <option value="Minuman" {{ old('category') == 'Minuman' ? 'selected' : '' }}>🥤 Minuman</option>
                                <option value="Rokok" {{ old('category') == 'Rokok' ? 'selected' : '' }}>🚬 Rokok</option>
                                <option value="Perlengkapan Mandi" {{ old('category') == 'Perlengkapan Mandi' ? 'selected' : '' }}>🧴 Perlengkapan Mandi</option>
                                <option value="Makanan Kaleng" {{ old('category') == 'Makanan Kaleng' ? 'selected' : '' }}>🥫 Makanan Kaleng</option>
                                <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>📦 Lainnya</option>
                            </select>
                            @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-balance-scale mr-2 text-blue-500"></i>
                                Satuan <span class="text-red-500">*</span>
                            </label>
                            <select name="unit" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                <option value="">Pilih Satuan</option>
                                <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs (Buah)</option>
                                <option value="pack" {{ old('unit') == 'pack' ? 'selected' : '' }}>Pack (Bungkus)</option>
                                <option value="botol" {{ old('unit') == 'botol' ? 'selected' : '' }}>Botol</option>
                                <option value="kaleng" {{ old('unit') == 'kaleng' ? 'selected' : '' }}>Kaleng</option>
                                <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kg (Kilogram)</option>
                                <option value="gram" {{ old('unit') == 'gram' ? 'selected' : '' }}>Gram</option>
                                <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                            </select>
                            @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <!-- Price & Purchase Price Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-2 text-green-500"></i>
                                Harga Jual <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="number" name="price" value="{{ old('price') }}" required
                                       class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                       placeholder="0">
                            </div>
                            @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-money-bill mr-2 text-orange-500"></i>
                                Harga Beli <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="number" name="purchase_price" value="{{ old('purchase_price') }}" required
                                       class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                       placeholder="0">
                            </div>
                            @error('purchase_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <!-- Stock & Min Stock Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-boxes mr-2 text-blue-500"></i>
                                Stok Awal <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stock" value="{{ old('stock', 0) }}" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                   min="0">
                            @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>
                                Stok Minimal <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                   min="0">
                            <p class="text-xs text-gray-400 mt-1">Peringatan akan muncul jika stok di bawah angka ini</p>
                            @error('min_stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <!-- Branch (Owner only) -->
                    @if(Auth::user()->hasRole('owner'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-store mr-2 text-blue-500"></i>
                            Cabang <span class="text-red-500">*</span>
                        </label>
                        <select name="branch_id" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Pilih Cabang</option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }} ({{ $branch->city }})
                            </option>
                            @endforeach
                        </select>
                        @error('branch_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @else
                    <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}">
                    @endif
                    
                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg shadow-md hover:shadow-lg transition">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function generateBarcode() {
        const timestamp = Date.now();
        const random = Math.floor(Math.random() * 10000);
        const barcode = '899' + timestamp.toString().slice(-6) + random.toString().padStart(4, '0');
        document.querySelector('input[name="barcode"]').value = barcode;
    }
</script>