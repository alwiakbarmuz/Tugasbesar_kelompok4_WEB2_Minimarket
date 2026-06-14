<x-app-layout title="Riwayat Stok">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Riwayat Stok') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Riwayat perubahan stok produk: <span class="font-medium text-gray-700">{{ $product->name }}</span>
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('products.show', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Produk</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Current Stock Card -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 mb-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-blue-100 text-sm">Stok Saat Ini</p>
                        <p class="text-3xl font-bold">{{ number_format($product->stock) }} <span class="text-lg">{{ $product->unit }}</span></p>
                        <p class="text-blue-100 text-sm mt-1">Minimal stok: {{ $product->min_stock }} {{ $product->unit }}</p>
                    </div>
                    <i class="fas fa-boxes text-5xl text-blue-200"></i>
                </div>
            </div>
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-arrow-down text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Stok Masuk</p>
                            <p class="text-xl font-bold text-gray-800">{{ number_format($logs->where('type', 'in')->sum('quantity')) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-arrow-up text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Stok Keluar</p>
                            <p class="text-xl font-bold text-gray-800">{{ number_format($logs->where('type', 'out')->sum('quantity')) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-adjust text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Penyesuaian</p>
                            <p class="text-xl font-bold text-gray-800">{{ number_format($logs->where('type', 'adjustment')->count()) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-history mr-2 text-blue-500"></i>
                        Riwayat Perubahan Stok
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700 border-b">
                            <tr>
                                <th class="px-6 py-3">Tanggal & Waktu</th>
                                <th class="px-6 py-3">Tipe</th>
                                <th class="px-6 py-3 text-center">Jumlah</th>
                                <th class="px-6 py-3 text-center">Stok Sebelum</th>
                                <th class="px-6 py-3 text-center">Stok Sesudah</th>
                                <th class="px-6 py-3">Petugas</th>
                                <th class="px-6 py-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                 </td>
                                <td class="px-6 py-4">
                                    @if($log->type == 'in')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-arrow-down mr-1"></i> Masuk
                                        </span>
                                    @elseif($log->type == 'out')
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-arrow-up mr-1"></i> Keluar
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-adjust mr-1"></i> Penyesuaian
                                        </span>
                                    @endif
                                 </td>
                                <td class="px-6 py-4 text-center font-semibold">
                                    {{ number_format($log->quantity) }}
                                 </td>
                                <td class="px-6 py-4 text-center">
                                    {{ number_format($log->stock_before) }}
                                 </td>
                                <td class="px-6 py-4 text-center font-semibold {{ $log->stock_after <= $product->min_stock ? 'text-red-600' : 'text-gray-800' }}">
                                    {{ number_format($log->stock_after) }}
                                 </td>
                                <td class="px-6 py-4">
                                    {{ $log->user->name ?? '-' }}
                                 </td>
                                <td class="px-6 py-4 text-gray-500 max-w-xs">
                                    <span class="line-clamp-2">{{ $log->note ?? '-' }}</span>
                                 </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-history text-4xl text-gray-300 mb-3 block"></i>
                                    <p>Belum ada riwayat perubahan stok</p>
                                 </td>
                             </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>