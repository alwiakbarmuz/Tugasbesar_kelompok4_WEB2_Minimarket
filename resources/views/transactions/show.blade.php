<x-app-layout title="Informasi Transaksi">
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Detail Transaksi') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Invoice: {{ $transaction->invoice_number }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('transactions.print', $transaction) }}" target="_blank"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-print"></i>
                    <span>Print</span>
                </a>
                <a href="{{ route('transactions.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <!-- Transaction Header -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-4 mb-4 border-b">
                        <div>
                            <p class="text-xs text-gray-500">Tanggal Transaksi</p>
                            <p class="font-semibold text-gray-800">{{ $transaction->transaction_date->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Kasir</p>
                            <p class="font-semibold text-gray-800">{{ $transaction->cashier->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Cabang</p>
                            <p class="font-semibold text-gray-800">{{ $transaction->branch->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            @if($transaction->status === 'completed')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Selesai
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i> Dibatalkan
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Transaction Items -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-800 mb-3">Detail Produk</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Produk</th>
                                        <th class="px-4 py-2 text-center">Qty</th>
                                        <th class="px-4 py-2 text-right">Harga</th>
                                        <th class="px-4 py-2 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transaction->details as $detail)
                                    <tr class="border-b">
                                        <td class="px-4 py-2">{{ $detail->product->name }}</td>
                                        <td class="px-4 py-2 text-center">{{ $detail->quantity }}</td>
                                        <td class="px-4 py-2 text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            <table>
                        </div>
                    </div>
                    
                    <!-- Transaction Summary -->
                    <div class="flex justify-end">
                        <div class="w-72 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Pajak (11%)</span>
                                <span>Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-2 border-t">
                                <span>Total</span>
                                <span class="text-blue-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tunai</span>
                                <span>Rp {{ number_format($transaction->cash, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-green-600 font-semibold">
                                <span>Kembali</span>
                                <span>Rp {{ number_format($transaction->change, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    @if($transaction->notes)
                    <div class="mt-6 p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Catatan:</p>
                        <p class="text-sm text-gray-700">{{ $transaction->notes }}</p>
                    </div>
                    @endif
                    
                    <!-- Cancel Button (Manager/Owner only) -->
                    @if($transaction->status === 'completed' && Auth::user()->can('cancel transactions'))
                    <div class="mt-6 pt-4 border-t">
                        <button type="button" 
                                onclick="confirmCancel('{{ $transaction->id }}', '{{ $transaction->invoice_number }}')"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-ban"></i>
                            <span>Batalkan Transaksi</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal dengan alasan -->
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
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Alasan Pembatalan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="cancelReason" rows="3" 
                            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-200"
                            placeholder="Masukkan alasan pembatalan..."></textarea>
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
    
    function submitCancel() {
        const reason = document.getElementById('cancelReason').value.trim();
        if (reason === '') {
            alert('Silakan masukkan alasan pembatalan');
            return;
        }
        document.getElementById('cancelReasonInput').value = reason;
        document.getElementById('cancelForm').action = '/transactions/' + currentCancelId + '/cancel';
        document.getElementById('cancelForm').submit();
    }
    
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
        document.getElementById('cancelModal').classList.remove('flex');
    }
</script>