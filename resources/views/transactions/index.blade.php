<x-app-layout title="Manajemen Transaksi">
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Riwayat Transaksi') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Lihat dan kelola semua transaksi
                </p>
            </div>
            @can('create transactions')
            <a href="{{ route('transactions.create') }}" 
               class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2 rounded-lg flex items-center space-x-2 shadow-md hover:shadow-lg transition">
                <i class="fas fa-cart-plus"></i>
                <span>Transaksi Baru</span>
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                    <p class="text-purple-100 text-sm">Rata-rata Transaksi</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($summary['avg_transaction'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white">
                    <p class="text-orange-100 text-sm">Total Pajak</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($summary['total_tax'], 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Search & Filter - Vertical Layout -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" class="space-y-4">
                    <!-- Baris 1: Search -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Cari Invoice</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" placeholder="Nomor invoice..." 
                                value="{{ request('search') }}"
                                class="w-full pl-10 pr-10 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm">
                            @if(request('search'))
                            <a href="{{ route('transactions.index') }}" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500">
                                <i class="fas fa-times-circle"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Baris 2: Date Range (2 kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 py-2 px-3 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 py-2 px-3 text-sm">
                        </div>
                    </div>
                    
                    <!-- Baris 3: Branch & Status (2 kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if(Auth::user()->hasRole('owner'))
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cabang</label>
                            <select name="branch_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 py-2 px-3 text-sm bg-white">
                                <option value="">Semua Cabang</option>
                                @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 py-2 px-3 text-sm bg-white">
                                <option value="">Semua Status</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Baris 4: Action Buttons (Wrap ke bawah) -->
                    <div class="flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                            <i class="fas fa-search text-sm"></i>
                            <span>Cari</span>
                        </button>
                        
                        <a href="{{ route('transactions.index', ['today' => 1]) }}" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-2">
                            <i class="fas fa-calendar-day text-sm"></i>
                            <span>Hari Ini</span>
                        </a>
                        
                        @if(request('search') || request('date_from') || request('date_to') || request('branch_id') || request('status'))
                        <a href="{{ route('transactions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                            <i class="fas fa-times text-sm"></i>
                            <span>Reset</span>
                        </a>
                        @endif
                        
                        <!-- Export Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                                <i class="fas fa-download text-sm"></i>
                                <span>Export</span>
                                <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-40 bg-white rounded-lg shadow-lg z-10 border" style="display: none;">
                                <a href="{{ route('reports.export', ['type'=>'daily', 'date'=>request('date_from', date('Y-m-d')), 'branch_id'=>request('branch_id'), 'format'=>'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                    <i class="fas fa-file-excel text-green-600 mr-2"></i> Excel (CSV)
                                </a>
                                <a href="{{ route('reports.export', ['type'=>'daily', 'date'=>request('date_from', date('Y-m-d')), 'branch_id'=>request('branch_id'), 'format'=>'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg">
                                    <i class="fas fa-file-pdf text-red-600 mr-2"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Baris 5: Active Filters (Jika ada filter aktif) -->
                    @php
                        $activeFilters = [];
                        if(request('search')) $activeFilters[] = 'Invoice: ' . request('search');
                        if(request('date_from')) $activeFilters[] = 'Dari: ' . date('d/m/Y', strtotime(request('date_from')));
                        if(request('date_to')) $activeFilters[] = 'Sampai: ' . date('d/m/Y', strtotime(request('date_to')));
                        if(request('branch_id') && Auth::user()->hasRole('owner')) {
                            $branch = $branches->firstWhere('id', request('branch_id'));
                            if($branch) $activeFilters[] = 'Cabang: ' . $branch->name;
                        }
                        if(request('status')) $activeFilters[] = 'Status: ' . (request('status') == 'completed' ? 'Selesai' : 'Dibatalkan');
                    @endphp
                    
                    @if(!empty($activeFilters))
                    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-100">
                        <span class="text-xs text-gray-400">Filter aktif:</span>
                        @foreach($activeFilters as $filter)
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">
                            {{ $filter }}
                        </span>
                        @endforeach
                        <a href="{{ route('transactions.index') }}" class="text-xs text-red-500 hover:text-red-700 ml-1">
                            <i class="fas fa-times-circle"></i> Hapus semua
                        </a>
                    </div>
                    @endif
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 border-b">
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
                            @forelse($transactions as $transaction)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $transaction->invoice_number }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 relative-time-cell" data-timestamp="{{ $transaction->transaction_date->timestamp }}">
                                    <span class="relative-time-display">{{ $transaction->transaction_date->format('d/m/Y H:i:s') }}</span>
                                    <span class="absolute-time hidden text-xs text-gray-400 ml-1">({{ $transaction->transaction_date->format('d/m/Y H:i:s') }})</span>
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
                                        <i class="fas fa-check-circle mr-1"></i> Selesai
                                    </span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Dibatalkan
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('transactions.show', $transaction) }}" 
                                           class="text-blue-600 hover:text-blue-800 transition" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('transactions.print', $transaction) }}" 
                                           target="_blank"
                                           class="text-green-600 hover:text-green-800 transition" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if($transaction->status === 'completed' && Auth::user()->can('cancel transactions'))
                                        <button type="button" 
                                                onclick="confirmCancel('{{ $transaction->id }}', '{{ $transaction->invoice_number }}')"
                                                class="text-red-600 hover:text-red-800 transition" title="Batalkan">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ Auth::user()->hasRole('owner') ? '7' : '6' }}" 
                                    class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-receipt text-4xl text-gray-300 mb-3 block"></i>
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
                <div class="px-6 py-4 border-t">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Batalkan Transaksi</h3>
                </div>
                
                <p class="text-gray-600 mb-3">
                    Apakah Anda yakin ingin membatalkan transaksi <span id="cancelInvoiceNumber" class="font-semibold"></span>?
                </p>
                
                <!-- Tambahkan field alasan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Alasan Pembatalan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="cancelReason" rows="3" 
                            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-200"
                            placeholder="Masukkan alasan pembatalan transaksi..."></textarea>
                    <p class="text-xs text-gray-400 mt-1">Alasan akan dicatat untuk audit</p>
                </div>
                
                <form id="cancelForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="cancel_reason" id="cancelReasonInput">
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCancelModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Batal
                        </button>
                        <button type="button" onclick="submitCancel()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            Ya, Batalkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Fungsi untuk mengupdate waktu relatif secara real-time
    function updateRelativeTimes() {
        const now = Math.floor(Date.now() / 1000);
        
        document.querySelectorAll('.relative-time-cell').forEach(cell => {
            const timestamp = parseInt(cell.dataset.timestamp);
            const diff = now - timestamp;
            const displaySpan = cell.querySelector('.relative-time-display');
            
            let text = '';
            
            if (diff < 5) {
                text = 'Baru saja';
            } else if (diff < 60) {
                text = `${diff} detik yang lalu`;
            } else if (diff < 3600) {
                const minutes = Math.floor(diff / 60);
                text = `${minutes} menit yang lalu`;
            } else if (diff < 86400) {
                const hours = Math.floor(diff / 3600);
                text = `${hours} jam yang lalu`;
            } else if (diff < 604800) {
                const days = Math.floor(diff / 86400);
                text = `${days} hari yang lalu`;
            } else {
                // Jika lebih dari 7 hari, tampilkan tanggal asli
                const date = new Date(timestamp * 1000);
                text = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
            
            if (displaySpan) {
                displaySpan.textContent = text;
            }
        });
    }
    
    // Update setiap detik
    updateRelativeTimes();
    setInterval(updateRelativeTimes, 1000);
    
    // Tooltip untuk melihat waktu asli saat hover
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

    let currentCancelId = null;
    let currentInvoiceNumber = null;
    
    function confirmCancel(id, invoiceNumber) {
        currentCancelId = id;
        currentInvoiceNumber = invoiceNumber;
        document.getElementById('cancelInvoiceNumber').textContent = invoiceNumber;
        document.getElementById('cancelModal').classList.remove('hidden');
        document.getElementById('cancelModal').classList.add('flex');
        document.getElementById('cancelReason').value = '';
    }
    
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
        document.getElementById('cancelModal').classList.remove('flex');
    }
    
    function submitCancel() {
        const reason = document.getElementById('cancelReason').value.trim();
        
        if (reason === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Alasan Wajib Diisi',
                text: 'Silakan masukkan alasan pembatalan transaksi.',
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        // Tampilkan loading
        Swal.fire({
            title: 'Memproses Pembatalan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Kirim request cancel
        fetch(`/transactions/${currentCancelId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                cancel_reason: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.error || 'Terjadi kesalahan saat membatalkan transaksi',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        });
    }
</script>