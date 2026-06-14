<x-app-layout title="Kelola Laporan Harian">
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Laporan Harian') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Ringkasan transaksi per hari
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Date Filter - Compact Version -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" class="space-y-4">
                    <!-- Baris 1: Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Tanggal -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Laporan</label>
                            <div class="relative">
                                <input type="date" name="date" value="{{ $date }}" 
                                      class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200 py-2 pl-3 pr-8 text-sm">
                                <i class="fas fa-calendar-alt absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                            </div>
                        </div>
                        
                        <!-- Cabang (Owner only) -->
                        @if(Auth::user()->hasRole('owner'))
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cabang</label>
                            <div class="relative">
                                <select name="branch_id" class="w-full rounded-lg border-gray-300 py-2 pl-3 pr-8 text-sm bg-white appearance-none">
                                    <option value="">Semua Cabang</option>
                                    @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Baris 2: Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                            <i class="fas fa-chart-line"></i> Tampilkan
                        </button>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                                <i class="fas fa-download"></i> Export
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-40 bg-white rounded-lg shadow-lg z-10 border" style="display: none;">
                                <a href="{{ route('reports.export', ['type'=>'daily', 'date'=>$date, 'branch_id'=>request('branch_id'), 'format'=>'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                    <i class="fas fa-file-excel text-green-600 mr-2"></i> Excel (CSV)
                                </a>
                                <a href="{{ route('reports.export', ['type'=>'daily', 'date'=>$date, 'branch_id'=>request('branch_id'), 'format'=>'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg">
                                    <i class="fas fa-file-pdf text-red-600 mr-2"></i> PDF
                                </a>
                            </div>
                        </div>
                        
                        <!-- Reset Button -->
                        @php
                            $defaultDate = date('Y-m-d');
                            $isDailyFilterActive = (request('date') && request('date') != $defaultDate) || request('branch_id');
                        @endphp
                        
                        @if($isDailyFilterActive)
                        <a href="{{ route('reports.daily') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg transition flex items-center gap-2 text-sm">
                            <i class="fas fa-times"></i> Reset
                        </a>
                        @endif
                    </div>
                    
                    <!-- Baris 3: Active Filters Badges -->
                    @if($isDailyFilterActive)
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs text-gray-400">Filter aktif:</span>
                            @if(request('date') && request('date') != $defaultDate)
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">
                                <i class="fas fa-calendar-day text-xs"></i> {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}
                                <a href="{{ route('reports.daily', array_merge(request()->except('date'), ['date' => $defaultDate])) }}" class="hover:text-red-500 ml-1">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </span>
                            @endif
                            @if(request('branch_id') && Auth::user()->hasRole('owner'))
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">
                                <i class="fas fa-store text-xs"></i> {{ optional($branches->firstWhere('id', request('branch_id')))->name }}
                                <a href="{{ route('reports.daily', array_merge(request()->except('branch_id'), ['branch_id' => null])) }}" class="hover:text-red-500 ml-1">
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
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-blue-100 text-sm">Total Transaksi</p>
                            <p class="text-2xl font-bold">{{ number_format($summary['total_transactions']) }}</p>
                        </div>
                        <i class="fas fa-receipt text-3xl text-blue-200"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-green-100 text-sm">Total Pendapatan</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</p>
                        </div>
                        <i class="fas fa-money-bill-wave text-3xl text-green-200"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-purple-100 text-sm">Rata-rata Transaksi</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($summary['avg_transaction'], 0, ',', '.') }}</p>
                        </div>
                        <i class="fas fa-chart-line text-3xl text-purple-200"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-orange-100 text-sm">Total Pajak</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($summary['total_tax'], 0, ',', '.') }}</p>
                        </div>
                        <i class="fas fa-percent text-3xl text-orange-200"></i>
                    </div>
                </div>
            </div>

            <!-- Hourly Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                    Jam Sibuk (Per Jam)
                </h3>
                <canvas id="hourlyChart" height="80"></canvas>
            </div>

            <!-- Top Products -->
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <div class="p-4 border-b bg-gray-50">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-crown mr-2 text-yellow-500"></i>
                        Produk Terlaris Hari Ini
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Produk</th>
                                <th class="px-6 py-3 text-center">Jumlah Terjual</th>
                                <th class="px-6 py-3 text-right">Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $product)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $product->name }}</td>
                                <td class="px-6 py-3 text-center font-semibold">{{ number_format($product->total_quantity) }}</td>
                                <td class="px-6 py-3 text-right">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada data produk terjual</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-list-ul mr-2 text-blue-500"></i>
                        Detail Transaksi
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left">Invoice</th>
                                <th class="px-6 py-3 text-left">Waktu</th>
                                @if(Auth::user()->hasRole('owner'))
                                <th class="px-6 py-3 text-left">Cabang</th>
                                @endif
                                <th class="px-6 py-3 text-left">Kasir</th>
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium">{{ $transaction->invoice_number }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $transaction->transaction_date->format('H:i:s') }}</td>
                                @if(Auth::user()->hasRole('owner'))
                                <td class="px-6 py-3">{{ $transaction->branch->name ?? '-' }}</td>
                                @endif
                                <td class="px-6 py-3">{{ $transaction->cashier->name ?? '-' }}</td>
                                <td class="px-6 py-3 text-right font-semibold">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if($transaction->status === 'completed')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Selesai</span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ Auth::user()->hasRole('owner') ? '6' : '5' }}" class="px-6 py-8 text-center text-gray-500">Tidak ada transaksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('hourlyChart'), {
        type: 'bar',
        data: {
            labels: @json(array_column($hourlyData, 'hour')),
            datasets: [{
                label: 'Jumlah Transaksi',
                data: @json(array_column($hourlyData, 'count')),
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                borderRadius: 4
            }, {
                label: 'Pendapatan (Rp)',
                data: @json(array_column($hourlyData, 'revenue')),
                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1,
                borderRadius: 4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw;
                            if (context.dataset.label.includes('Pendapatan')) {
                                return label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                            return label + ': ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Jumlah Transaksi' }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    title: { display: true, text: 'Pendapatan (Rp)' },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });
</script>