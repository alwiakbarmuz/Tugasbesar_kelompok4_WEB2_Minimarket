<x-app-layout title="Kelola Laporan Bulanan">
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Laporan Bulanan') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Ringkasan transaksi per bulan
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter - Compact Version -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" class="space-y-4">
                    <!-- Baris 1: Select Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Bulan -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                            <div class="relative">
                                <select name="month" class="w-full rounded-lg border-gray-300 py-2 pl-3 pr-8 text-sm bg-white appearance-none">
                                    @foreach($months as $k=>$m)
                                    <option value="{{ $k }}" {{ $month == $k ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Tahun -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
                            <div class="relative">
                                <select name="year" class="w-full rounded-lg border-gray-300 py-2 pl-3 pr-8 text-sm bg-white appearance-none">
                                    @foreach($availableYears as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
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
                                <a href="{{ route('reports.export', ['type'=>'monthly', 'year'=>$year, 'month'=>$month, 'branch_id'=>request('branch_id'), 'format'=>'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                    <i class="fas fa-file-excel text-green-600 mr-2"></i> Excel (CSV)
                                </a>
                                <a href="{{ route('reports.export', ['type'=>'monthly', 'year'=>$year, 'month'=>$month, 'branch_id'=>request('branch_id'), 'format'=>'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg">
                                    <i class="fas fa-file-pdf text-red-600 mr-2"></i> PDF
                                </a>
                            </div>
                        </div>
                        
                        <!-- Reset Button -->
                        @php
                            $isFilterActive = (request('year') && request('year') != date('Y')) || 
                                              (request('month') && request('month') != date('m')) || 
                                              request('branch_id');
                        @endphp
                        
                        @if($isFilterActive)
                        <a href="{{ route('reports.monthly') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg transition flex items-center gap-2 text-sm">
                            <i class="fas fa-times"></i> Reset
                        </a>
                        @endif
                    </div>
                    
                    <!-- Baris 3: Active Filters Badges -->
                    @if($isFilterActive)
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs text-gray-400">Filter aktif:</span>
                            
                            <!-- Filter Bulan - FIX: Cast ke int untuk menghindari error -->
                            @if(request('month') && request('month') != date('m'))
                            @php
                                $monthValue = is_numeric(request('month')) ? (int)request('month') : (int)date('m');
                                $monthName = $months[$monthValue] ?? $months[(int)date('m')];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">
                                <i class="fas fa-calendar-alt text-xs"></i> {{ $monthName }}
                                <a href="{{ route('reports.monthly', array_merge(request()->except('month'), ['month' => date('m')])) }}" class="hover:text-red-500 ml-1">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </span>
                            @endif
                            
                            <!-- Filter Tahun -->
                            @if(request('year') && request('year') != date('Y'))
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">
                                <i class="fas fa-calendar text-xs"></i> {{ request('year') }}
                                <a href="{{ route('reports.monthly', array_merge(request()->except('year'), ['year' => date('Y')])) }}" class="hover:text-red-500 ml-1">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </span>
                            @endif
                            
                            <!-- Filter Cabang -->
                            @if(request('branch_id') && Auth::user()->hasRole('owner'))
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">
                                <i class="fas fa-store text-xs"></i> 
                                {{ optional($branches->firstWhere('id', request('branch_id')))->name ?? 'Cabang' }}
                                <a href="{{ route('reports.monthly', array_merge(request()->except('branch_id'), ['branch_id' => null])) }}" class="hover:text-red-500 ml-1">
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
                    <p class="text-blue-100 text-sm">Total Transaksi</p>
                    <p class="text-2xl font-bold">{{ number_format($summary['total_transactions']) }}</p>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white">
                    <p class="text-green-100 text-sm">Total Pendapatan</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white">
                    <p class="text-purple-100 text-sm">Rata-rata Harian</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($summary['avg_daily_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white">
                    <p class="text-orange-100 text-sm">Hari Terbaik</p>
                    @if($summary['best_day'])
                    <p class="text-lg font-bold">{{ $summary['best_day']['date_formatted'] }}</p>
                    <p class="text-xs">Rp {{ number_format($summary['best_day']['revenue'], 0, ',', '.') }}</p>
                    @else
                    <p class="text-sm">-</p>
                    @endif
                </div>
            </div>

            <!-- Daily Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-line mr-2 text-blue-500"></i>
                    Pendapatan Harian - {{ $months[(int)$month] ?? $months[date('m')] }} {{ $year }}
                </h3>
                <canvas id="dailyChart" height="100"></canvas>
            </div>

            <!-- Branch Comparison (Owner only) -->
            @if(!empty($branchComparison) && Auth::user()->hasRole('owner'))
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <div class="p-4 border-b bg-gray-50">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-chart-pie mr-2 text-purple-500"></i>
                        Perbandingan Antar Cabang
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Cabang</th>
                                <th class="px-6 py-3 text-center">Jumlah Transaksi</th>
                                <th class="px-6 py-3 text-right">Total Pendapatan</th>
                                <th class="px-6 py-3 text-right">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalRevenue = array_sum(array_column($branchComparison, 'revenue')); @endphp
                            @foreach($branchComparison as $bc)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium">{{ $bc['branch'] }}</td>
                                <td class="px-6 py-3 text-center">{{ number_format($bc['transactions']) }}</td>
                                <td class="px-6 py-3 text-right">Rp {{ number_format($bc['revenue'], 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-right">
                                    @php $percentage = $totalRevenue > 0 ? ($bc['revenue'] / $totalRevenue) * 100 : 0; @endphp
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-sm">{{ number_format($percentage, 1) }}%</span>
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 rounded-full h-2" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Daily Data Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-table mr-2 text-blue-500"></i>
                        Data Harian - {{ $months[(int)$month] ?? $months[date('m')] }} {{ $year }}
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left">Tanggal</th>
                                <th class="px-6 py-3 text-center">Jumlah Transaksi</th>
                                <th class="px-6 py-3 text-right">Total Pendapatan</th>
                                <th class="px-6 py-3 text-right">Rata-rata Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyData as $day)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium">{{ $day['date_formatted'] }}</td>
                                <td class="px-6 py-3 text-center">{{ number_format($day['count']) }}</td>
                                <td class="px-6 py-3 text-right">Rp {{ number_format($day['revenue'], 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-right">
                                    @php $avg = $day['count'] > 0 ? $day['revenue'] / $day['count'] : 0; @endphp
                                    Rp {{ number_format($avg, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Tidak ada data</td></tr>
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
    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: @json(array_column($dailyData, 'date_formatted')),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: @json(array_column($dailyData, 'revenue')),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
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