<x-app-layout title="Audit Transaksi">
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Transaksi Dibatalkan') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Riwayat transaksi yang dibatalkan
                </p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- Search -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cari Invoice</label>
                            <input type="text" name="search" placeholder="Nomor invoice..." 
                                   value="{{ request('search') }}"
                                   class="w-full rounded-lg border-gray-300 py-2 px-3 text-sm">
                        </div>
                        
                        <!-- Branch Filter -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cabang</label>
                            <select name="branch_id" class="w-full rounded-lg border-gray-300 py-2 px-3 text-sm bg-white">
                                <option value="">Semua Cabang</option>
                                @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Date From -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="w-full rounded-lg border-gray-300 py-2 px-3 text-sm">
                        </div>
                        
                        <!-- Date To -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="w-full rounded-lg border-gray-300 py-2 px-3 text-sm">
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        @if(request('search') || request('branch_id') || request('date_from') || request('date_to'))
                        <a href="{{ route('audit.transactions') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-times"></i> Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left w-[10%]">Invoice</th>
                                <th class="px-4 py-3 text-left w-[12%]">Tgl Transaksi</th>
                                <th class="px-4 py-3 text-left w-[10%]">Cabang</th>
                                <th class="px-4 py-3 text-left w-[10%]">Kasir</th>
                                <th class="px-4 py-3 text-left w-[12%]">Tgl Dibatalkan</th>
                                <th class="px-4 py-3 text-left w-[10%]">Dibatalkan Oleh</th>
                                <th class="px-4 py-3 text-left w-[20%]">Alasan</th>
                                <th class="px-4 py-3 text-center w-[6%]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $transaction->invoice_number }}</td>
                                <td class="px-4 py-3">{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">{{ $transaction->branch->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $transaction->cashier->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-red-600">{{ $transaction->deleted_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">{{ $transaction->deletedBy->name ?? '-' }}</td>
                                <td class="px-4 py-3 max-w-xs break-words">{{ $transaction->delete_reason ?? '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <button onclick="restoreTransaction({{ $transaction->id }}, '{{ $transaction->invoice_number }}')" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-green-600 hover:bg-green-100 transition"
                                                title="Kembalikan Transaksi">
                                            <i class="fas fa-undo-alt text-sm"></i>
                                        </button>
                                        <button onclick="forceDeleteTransaction({{ $transaction->id }}, '{{ $transaction->invoice_number }}')" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-100 transition"
                                                title="Hapus Permanen">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-trash-alt text-4xl text-gray-300 mb-3 block"></i>
                                    <p>Belum ada transaksi yang dibatalkan</p>
                                 </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function restoreTransaction(id, invoiceNumber) {
        Swal.fire({
            title: 'Kembalikan Transaksi?',
            html: `Apakah Anda yakin ingin mengembalikan transaksi <strong>${invoiceNumber}</strong>?<br><br>
                   <span class="text-orange-600">⚠️ Stok produk akan dikurangi kembali.</span>`,
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
                
                fetch(`/audit/transactions/${id}/restore`, { 
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
                            text: data.message || 'Transaksi berhasil dikembalikan',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.error || 'Gagal mengembalikan transaksi',
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
    
    function forceDeleteTransaction(id, invoiceNumber) {
        Swal.fire({
            title: 'Hapus Permanen!',
            html: `Apakah Anda yakin ingin menghapus <strong>PERMANEN</strong> transaksi <strong>${invoiceNumber}</strong>?<br><br>
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
                
                fetch(`/audit/transactions/${id}/force-delete`, { 
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
                            text: data.message || 'Transaksi berhasil dihapus permanen',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.error || 'Gagal menghapus transaksi',
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