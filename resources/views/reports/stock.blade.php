<x-app-layout title="Kelola Laporan Stok">
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Laporan Stok') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Status stok semua produk
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
           <!-- Filters - Compact Version -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET">
                    <!-- Grid untuk filter -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <!-- Search -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cari Produk</label>
                            <div class="relative">
                                <input type="text" name="search" placeholder="Nama produk atau barcode..." 
                                      value="{{ request('search') }}"
                                      class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 text-sm">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                            </div>
                        </div>
                        
                        @if(Auth::user()->hasRole('owner'))
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cabang</label>
                            <select name="branch_id" class="w-full rounded-lg border-gray-300 py-2 px-3 text-sm bg-white">
                                <option value="">Semua Cabang</option>
                                @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status Stok</label>
                            <select name="stock_status" class="w-full rounded-lg border-gray-300 py-2 px-3 text-sm bg-white">
                                <option value="">Semua Stok</option>
                                <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>⚠️ Stok Menipis</option>
                                <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>❌ Stok Habis</option>
                                <option value="good" {{ request('stock_status') == 'good' ? 'selected' : '' }}>✅ Stok Aman</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                            <select name="category" class="w-full rounded-lg border-gray-300 py-2 px-3 text-sm bg-white">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $c)
                                <option value="{{ $c->category }}" {{ request('category') == $c->category ? 'selected' : '' }}>{{ $c->category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Tombol - Responsive wrap -->
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                                <i class="fas fa-download"></i> Export
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-40 bg-white rounded-lg shadow-lg z-10 border" style="display: none;">
                                <a href="{{ route('reports.export', ['type'=>'stock', 'branch_id'=>request('branch_id'), 'format'=>'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                    <i class="fas fa-file-excel text-green-600 mr-2"></i> Excel
                                </a>
                                <a href="{{ route('reports.export', ['type'=>'stock', 'branch_id'=>request('branch_id'), 'format'=>'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg">
                                    <i class="fas fa-file-pdf text-red-600 mr-2"></i> PDF
                                </a>
                            </div>
                        </div>
                        
                        @php
                            $hasFilters = request('search') || request('branch_id') || request('stock_status') || request('category');
                        @endphp
                        
                        @if($hasFilters)
                        <a href="{{ route('reports.stock') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg transition flex items-center gap-2 text-sm">
                            <i class="fas fa-times"></i> Reset Filter
                        </a>
                        @endif
                    </div>
                    
                    <!-- Active Filters Badges -->
                    @if($hasFilters)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs text-gray-400">Filter aktif:</span>
                            @if(request('search'))
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">
                                <i class="fas fa-search text-xs"></i> {{ request('search') }}
                                <a href="{{ route('reports.stock', array_merge(request()->except('search'), ['search' => null])) }}" class="hover:text-red-500 ml-1">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </span>
                            @endif
                            @if(request('branch_id') && Auth::user()->hasRole('owner'))
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">
                                <i class="fas fa-store text-xs"></i> {{ optional($branches->firstWhere('id', request('branch_id')))->name }}
                                <a href="{{ route('reports.stock', array_merge(request()->except('branch_id'), ['branch_id' => null])) }}" class="hover:text-red-500 ml-1">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </span>
                            @endif
                            @if(request('stock_status'))
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">
                                <i class="fas fa-chart-line text-xs"></i> 
                                {{ request('stock_status') == 'low' ? 'Stok Menipis' : (request('stock_status') == 'out' ? 'Stok Habis' : 'Stok Aman') }}
                                <a href="{{ route('reports.stock', array_merge(request()->except('stock_status'), ['stock_status' => null])) }}" class="hover:text-red-500 ml-1">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </span>
                            @endif
                            @if(request('category'))
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">
                                <i class="fas fa-folder text-xs"></i> {{ request('category') }}
                                <a href="{{ route('reports.stock', array_merge(request()->except('category'), ['category' => null])) }}" class="hover:text-red-500 ml-1">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </span>
                            @endif
                        </div>
                    </div>
                    @endif
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white">
                    <p class="text-blue-100 text-sm">Total Produk</p>
                    <p class="text-2xl font-bold">{{ number_format($summary['total_products']) }}</p>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white">
                    <p class="text-green-100 text-sm">Nilai Stok (Modal)</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($summary['total_stock_value'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
                    <p class="text-yellow-100 text-sm">Stok Menipis</p>
                    <p class="text-2xl font-bold">{{ $summary['low_stock_count'] }}</p>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white">
                    <p class="text-red-100 text-sm">Stok Habis</p>
                    <p class="text-2xl font-bold">{{ $summary['out_of_stock_count'] }}</p>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-pie mr-2 text-blue-500"></i>
                    Distribusi Produk per Kategori
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Chart Container - Lebih kecil dan proporsional -->
                    <div class="flex justify-center">
                        <div class="w-full max-w-[280px]">
                            <canvas id="categoryChart" height="280"></canvas>
                        </div>
                    </div>
                    
                    <!-- Legend/Keterangan - Lebih rapi -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Detail Kategori</h4>
                        <div class="space-y-2 max-h-[280px] overflow-y-auto pr-2">
                            @foreach($categories as $index => $cat)
                            @php
                                $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#84cc16'];
                                $color = $colors[$index % count($colors)];
                                $percentage = ($cat->total / $summary['total_products']) * 100;
                            @endphp
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $color }}"></div>
                                    <span class="text-sm text-gray-700">{{ $cat->category }}</span>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm font-semibold text-gray-800">{{ number_format($cat->total) }}</span>
                                    <span class="text-xs text-gray-500 w-12 text-right">{{ number_format($percentage, 1) }}%</span>
                                    <div class="w-24 bg-gray-200 rounded-full h-1.5">
                                        <div class="rounded-full h-1.5" style="width: {{ $percentage }}%; background-color: {{ $color }}"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Total summary -->
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-700">Total Produk</span>
                                <span class="text-lg font-bold text-blue-600">{{ number_format($summary['total_products']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3">Barcode</th>
                                <th class="px-6 py-3">Produk</th>
                                <th class="px-6 py-3">Kategori</th>
                                @if(Auth::user()->hasRole('owner'))
                                <th class="px-6 py-3">Cabang</th>
                                @endif
                                <th class="px-6 py-3 text-center">Stok</th>
                                <th class="px-6 py-3 text-right">Harga Jual</th>
                                <th class="px-6 py-3 text-right">Nilai Stok</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-3 font-mono text-xs">{{ $product->barcode }}</td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-800">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $product->unit }}</div>
                                </td>
                                <td class="px-6 py-3">{{ $product->category }}</td>
                                @if(Auth::user()->hasRole('owner'))
                                <td class="px-6 py-3">{{ $product->branch->name ?? '-' }}</td>
                                @endif
                                <td class="px-6 py-3 text-center">
                                    <span class="font-bold {{ $product->stock <= $product->min_stock ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ number_format($product->stock) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-right">Rp {{ number_format($product->stock * $product->purchase_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if($product->stock == 0)
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Habis</span>
                                    @elseif($product->stock <= $product->min_stock)
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Menipis</span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Aman</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ Auth::user()->hasRole('owner') ? '8' : '7' }}" class="px-6 py-8 text-center text-gray-500">Tidak ada produk\n</td></tr>
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
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json($categories->pluck('category')),
            datasets: [{
                data: @json($categories->pluck('total')),
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = @json($summary['total_products']);
                            let percentage = ((context.raw / total) * 100).toFixed(1);
                            return context.label + ': ' + context.raw + ' produk (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
</script>