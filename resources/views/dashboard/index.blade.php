<x-app-layout title="Dashboard">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard Minimarket') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Selamat datang kembali, <span class="font-medium text-gray-700">{{ Auth::user()->name }}</span>!
                </p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500" id="real-time-date"></div>
                <div class="text-xs text-gray-400 font-mono font-semibold" id="real-time-clock"></div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Cabang (khusus owner) -->
            @if(Auth::user()->hasRole('owner'))
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <form method="GET" class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">Filter Cabang:</label>
                    <select name="branch_id" onchange="this.form.submit()" 
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                        @endforeach
                    </select>
                    @if(request('branch_id'))
                    <a href="{{ route('dashboard') }}" class="text-sm text-red-600 hover:text-red-800">
                        Reset Filter
                    </a>
                    @endif
                </form>
            </div>
            @endif

            <!-- Stats Cards Row 1 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Card Transaksi Hari Ini (kecuali Warehouse) -->
                @if(!Auth::user()->hasRole('warehouse'))
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Transaksi Hari Ini</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($todayTransactions) }}</p>
                                @if(isset($transactionsTrend))
                                <div class="flex items-center mt-3">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    <span class="text-sm text-green-600 ml-1">{{ $transactionsTrend }}</span>
                                    <span class="text-xs text-gray-400 ml-2">vs kemarin</span>
                                </div>
                                @endif
                            </div>
                            <div class="rounded-full p-3 bg-blue-100">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 21h6M4 3h16"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Card Pendapatan Hari Ini (kecuali Warehouse) -->
                @if(!Auth::user()->hasRole('warehouse'))
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pendapatan Hari Ini</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                                @if(isset($revenueTrend))
                                <div class="flex items-center mt-3">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    <span class="text-sm text-green-600 ml-1">{{ $revenueTrend }}</span>
                                    <span class="text-xs text-gray-400 ml-2">vs kemarin</span>
                                </div>
                                @endif
                            </div>
                            <div class="rounded-full p-3 bg-green-100">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Card Stok Menipis (Semua Role) -->
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Stok Menipis</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $lowStockProducts }}</p>
                                @if($lowStockProducts > 0)
                                <p class="text-xs text-orange-600 mt-2">Perlu segera di-restock</p>
                                @endif
                            </div>
                            <div class="rounded-full p-3 bg-yellow-100">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Total Produk (Semua Role) -->
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Produk</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalProducts) }}</p>
                                <p class="text-xs text-gray-500 mt-2">Total stok: {{ number_format($totalStock) }}</p>
                            </div>
                            <div class="rounded-full p-3 bg-purple-100">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards Row 2 (Additional Metrics) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Card Stok Habis (Semua Role) -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-red-100 text-sm">Stok Habis</p>
                            <p class="text-3xl font-bold mt-1">{{ $outOfStockProducts }}</p>
                            @if($outOfStockProducts > 0)
                            <p class="text-xs text-red-200 mt-2">Produk perlu segera diisi</p>
                            @endif
                        </div>
                        <svg class="w-12 h-12 text-red-200 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                    </div>
                </div>

                <!-- Card Rata-rata Transaksi (kecuali Warehouse) -->
                @if(!Auth::user()->hasRole('warehouse'))
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-blue-100 text-sm">Rata-rata Transaksi</p>
                            <p class="text-3xl font-bold mt-1">Rp {{ number_format($avgTransactionValue, 0, ',', '.') }}</p>
                            <p class="text-xs text-blue-200 mt-2">Per transaksi</p>
                        </div>
                        <svg class="w-12 h-12 text-blue-200 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Card Produk Terjual Hari Ini (kecuali Warehouse) -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-green-100 text-sm">Produk Terjual</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($productsSoldToday) }}</p>
                            <p class="text-xs text-green-200 mt-2">Unit hari ini</p>
                        </div>
                        <svg class="w-12 h-12 text-green-200 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                @endif
                
                <!-- Card Nilai Stok (untuk Warehouse) -->
                @if(Auth::user()->hasRole('warehouse'))
                <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-teal-100 text-sm">Nilai Stok (Modal)</p>
                            <p class="text-3xl font-bold mt-1">Rp {{ number_format($totalStockValue ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-teal-200 mt-2">Total nilai pembelian</p>
                        </div>
                        <svg class="w-12 h-12 text-teal-200 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                @endif
            </div>

            <!-- Alert Stok Menipis -->
            @if($lowStockProducts > 0)
            <div class="mb-6">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <span class="font-medium">Peringatan Stok!</span>
                                Terdapat {{ $lowStockProducts }} produk dengan stok menipis 
                                @if($outOfStockProducts > 0)
                                dan {{ $outOfStockProducts }} produk habis.
                                @else
                                .
                                @endif
                                <a href="{{ route('products.index', ['stock_status' => 'low']) }}" class="font-medium underline hover:text-yellow-800 ml-1">
                                    Lihat detail →
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Chart 7 Hari Terakhir (kecuali Warehouse) -->
            @if(!Auth::user()->hasRole('warehouse'))
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Pendapatan 7 Hari Terakhir</h3>
                    <p class="text-sm text-gray-500 mt-1">Grafik pendapatan harian</p>
                </div>
                <div class="p-6">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
            @endif

           <!-- Chart Distribusi Stok (untuk Warehouse) -->
            @if(Auth::user()->hasRole('warehouse') && isset($categoryStockData))
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <div class="flex items-center space-x-2 mb-4">
                    <i class="fas fa-chart-pie text-blue-500 text-lg"></i>
                    <i class="fas fa-boxes text-blue-500 text-lg"></i>
                    <h3 class="font-semibold text-gray-800">Distribusi Stok per Kategori</h3>
                </div>
                
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Chart Container -->
                    <div class="flex justify-center md:w-1/2">
                        <div style="width: 240px; height: 240px;">
                            <canvas id="stockChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Legend/Keterangan -->
                    <div class="flex-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($categoryStockData['labels'] as $index => $label)
                            @php
                                $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#84cc16'];
                                $color = $colors[$index % count($colors)];
                                $value = $categoryStockData['data'][$index];
                                $percentage = ($value / $totalStock) * 100;
                            @endphp
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $color }}"></div>
                                    <span class="text-sm text-gray-700 truncate max-w-[100px]">{{ $label }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold text-gray-800">{{ number_format($value) }}</span>
                                    <span class="text-xs text-gray-500 w-10 text-right">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Total summary -->
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-700">Total Stok</span>
                                <span class="text-lg font-bold text-blue-600">{{ number_format($totalStock) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- PRODUK SECTION (untuk Warehouse)           -->
            <!-- ========================================== -->
            @if(Auth::user()->hasRole('warehouse') && isset($lowStockProductList))
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-boxes mr-2 text-blue-500"></i>
                                Status Stok Produk
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Produk dengan stok menipis dan stok habis</p>
                        </div>
                        <a href="{{ route('products.index') }}" 
                           class="text-sm text-blue-600 hover:text-blue-800 transition flex items-center space-x-1">
                            <span>Lihat Semua Produk</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Barcode</th>
                                <th class="px-6 py-3">Produk</th>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3 text-center">Stok Saat Ini</th>
                                <th class="px-6 py-3 text-center">Stok Minimal</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProductList as $product)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->barcode }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $product->unit }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100">{{ $product->category }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold {{ $product->stock == 0 ? 'text-red-600' : 'text-orange-600' }}">
                                        {{ number_format($product->stock) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ number_format($product->min_stock) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($product->stock == 0)
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-ban mr-1"></i> Stok Habis
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Stok Menipis
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('products.show', $product) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-check-circle text-4xl text-green-300 mb-3 block"></i>
                                    <p>Semua produk dalam kondisi stok aman</p>
                                    <p class="text-xs mt-1">Tidak ada produk dengan stok menipis atau habis</p>
                                </td>
                             </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- TRANSAKSI SECTION (untuk selain Warehouse) -->
            <!-- ========================================== -->
            @if(!Auth::user()->hasRole('warehouse'))
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>
                            <p class="text-sm text-gray-500 mt-1">10 transaksi terakhir</p>
                        </div>
                        @can('view transactions')
                        <a href="{{ route('transactions.index') }}" 
                           class="text-sm text-blue-600 hover:text-blue-800 transition flex items-center space-x-1">
                            <span>Lihat Semua</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        @endcan
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Invoice</th>
                                <th class="px-6 py-3">Tanggal & Waktu</th>
                                @if(Auth::user()->hasRole('owner'))
                                <th class="px-6 py-3">Cabang</th>
                                @endif
                                <th class="px-6 py-3">Kasir</th>
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $transaction->invoice_number }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 relative-time-cell" data-timestamp="{{ $transaction->transaction_date->timestamp }}">
                                    <span class="relative-time-display">{{ $transaction->transaction_date->diffForHumans() }}</span>
                                </td>
                                @if(Auth::user()->hasRole('owner'))
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                        {{ $transaction->branch->name ?? '-' }}
                                    </span>
                                </td>
                                @endif
                                <td class="px-6 py-4">
                                    {{ $transaction->cashier->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($transaction->total, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($transaction->status === 'completed')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                        Dibatalkan
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        @can('view transactions')
                                        <a href="{{ route('transactions.show', $transaction) }}" 
                                           class="text-blue-600 hover:text-blue-800 transition"
                                           title="Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        @endcan
                                        <a href="{{ route('transactions.print', $transaction) }}" 
                                           target="_blank"
                                           class="text-green-600 hover:text-green-800 transition"
                                           title="Print">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ Auth::user()->hasRole('owner') ? '7' : '6' }}" 
                                    class="px-6 py-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p>Belum ada transaksi</p>
                                    @can('create transactions')
                                    <a href="{{ route('transactions.create') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                                        Buat transaksi pertama →
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let lastUpdateCount = 0;
    
    function updateRelativeTimes() {
        const now = Math.floor(Date.now() / 1000);
        let updatedCount = 0;
        
        document.querySelectorAll('.relative-time-cell').forEach(cell => {
            const timestamp = parseInt(cell.dataset.timestamp);
            const diff = now - timestamp;
            const displaySpan = cell.querySelector('.relative-time-display');
            
            let oldText = displaySpan.textContent;
            let newText = '';
            
            if (diff < 5) {
                newText = 'Baru saja';
            } else if (diff < 60) {
                newText = `${diff} detik yang lalu`;
            } else if (diff < 3600) {
                const minutes = Math.floor(diff / 60);
                newText = `${minutes} menit yang lalu`;
            } else if (diff < 86400) {
                const hours = Math.floor(diff / 3600);
                newText = `${hours} jam yang lalu`;
            } else if (diff < 604800) {
                const days = Math.floor(diff / 86400);
                newText = `${days} hari yang lalu`;
            } else {
                const date = new Date(timestamp * 1000);
                newText = date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
            }
            
            if (oldText !== newText) {
                displaySpan.textContent = newText;
                displaySpan.classList.add('time-updated');
                updatedCount++;
                setTimeout(() => {
                    displaySpan.classList.remove('time-updated');
                }, 300);
            }
        });
        
        // Update title di tab browser (opsional)
        if (updatedCount > 0) {
            const title = document.title;
            if (!title.startsWith('●')) {
                document.title = '● ' + title;
                setTimeout(() => {
                    document.title = title.replace('● ', '');
                }, 1000);
            }
        }
    }
    
    // Update setiap detik
    updateRelativeTimes();
    setInterval(updateRelativeTimes, 1000);
    
    // Tooltip dengan waktu lengkap
    document.querySelectorAll('.relative-time-cell').forEach(cell => {
        const timestamp = parseInt(cell.dataset.timestamp);
        const date = new Date(timestamp * 1000);
        const formattedDate = date.toLocaleDateString('id-ID', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        cell.setAttribute('title', formattedDate);
    });

    // Real-time clock
    function getCurrentWIB() {
        const now = new Date();
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const dayName = days[now.getDay()];
        const day = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        const formattedDate = `${dayName}, ${day} ${month} ${year}`;
        
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const formattedTime = `${hours}:${minutes}:${seconds} WIB`;
        
        return { date: formattedDate, time: formattedTime };
    }
    
    function updateRealTimeDisplay() {
        const wib = getCurrentWIB();
        const dateElement = document.getElementById('real-time-date');
        const clockElement = document.getElementById('real-time-clock');
        
        if (dateElement) dateElement.textContent = wib.date;
        if (clockElement) clockElement.textContent = wib.time;
    }
    
    updateRealTimeDisplay();
    setInterval(updateRealTimeDisplay, 1000);
</script>

@if(!Auth::user()->hasRole('warehouse'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: @json($chartData['values']),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { 
                        position: 'top',
                        labels: { 
                            usePointStyle: true,
                            boxWidth: 8,
                            font: { size: 11 }
                        } 
                    },
                    tooltip: { 
                        callbacks: { 
                            label: function(context) { 
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw); 
                            } 
                        },
                        bodyFont: { size: 11 }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { 
                            callback: function(value) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value); },
                            font: { size: 10 }
                        }, 
                        grid: { color: '#e5e7eb' },
                        title: { display: true, text: 'Pendapatan (Rp)', font: { size: 10 } }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
@endif

@if(Auth::user()->hasRole('warehouse') && isset($categoryStockData))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('stockChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($categoryStockData['labels']),
                datasets: [{
                    data: @json($categoryStockData['data']),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#84cc16'],
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '60%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        bodyFont: { size: 11 },
                        callbacks: {
                            label: function(context) {
                                let total = @json($totalStock);
                                let percentage = ((context.raw / total) * 100).toFixed(1);
                                return context.label + ': ' + context.raw + ' unit (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endif