<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Transaksi - {{ $transaction->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', 'Lucida Console', monospace;
            width: 300px;
            margin: 0 auto;
            padding: 15px;
            background: white;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .border-top { border-top: 1px dashed #000; }
        .border-bottom { border-bottom: 1px dashed #000; }
        .border-double { border-top: 2px double #000; }
        .font-bold { font-weight: bold; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .pt-1 { padding-top: 5px; }
        .pt-2 { padding-top: 10px; }
        .pb-1 { padding-bottom: 5px; }
        .pb-2 { padding-bottom: 10px; }
        .logo {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 10px;
            letter-spacing: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 4px 0;
        }
        .item-name {
            font-size: 11px;
        }
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <!-- Header -->
    <div class="text-center">
        <div class="logo">MINIMARKET JAYUSMAN</div>
        <div class="receipt-title">STRUK PEMBELIAN</div>
        <div class="border-top mt-1"></div>
        <p>{{ $transaction->branch->address }}</p>
        <p>Telp: {{ $transaction->branch->phone }}</p>
        <div class="border-top mt-1"></div>
        <p>{{ $transaction->invoice_number }}</p>
        <p>{{ $transaction->transaction_date->format('d/m/Y H:i:s') }}</p>
        <p>Kasir: {{ $transaction->cashier->name }}</p>
        <div class="border-top mt-1"></div>
    </div>

    <!-- Items -->
    <table class="mt-1">
        <thead>
            <tr>
                <th class="text-left">Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->details as $detail)
            <tr>
                <td class="text-left item-name">{{ Str::limit($detail->product->name, 25) }}</td>
                <td class="text-center">{{ $detail->quantity }}</td>
                <td class="text-right">{{ number_format($detail->price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="border-top mt-1 pt-1"></div>
    <table>
        <tr>
            <td class="text-left">Subtotal</td>
            <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($transaction->discount > 0)
        <tr>
            <td class="text-left">Diskon</td>
            <td class="text-right">Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($transaction->tax > 0)
        <tr>
            <td class="text-left">Pajak (11%)</td>
            <td class="text-right">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="font-bold">
            <td class="text-left">TOTAL</td>
            <td class="text-right">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-left">Tunai</td>
            <td class="text-right">Rp {{ number_format($transaction->cash, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-left">Kembali</td>
            <td class="text-right">Rp {{ number_format($transaction->change, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="border-top mt-2 pt-1"></div>
    <div class="text-center mt-1">
        <p>Terima kasih atas kunjungan Anda</p>
        <p class="mt-1">*** {{ $transaction->status === 'completed' ? 'LUNAS' : 'DIBATALKAN' }} ***</p>
        @if($transaction->status === 'cancelled')
        <p class="mt-1 text-center" style="color: red;">TRANSAKSI DIBATALKAN</p>
        @endif
        <p class="mt-2">☎️ {{ $transaction->branch->phone }}</p>
        <p class="mb-1">🕐 Layanan 24 Jam</p>
        <div class="border-top mt-1"></div>
        <p class="mt-1" style="font-size: 10px;">Simpan struk ini sebagai bukti pembayaran</p>
    </div>

    <!-- Auto close after print (optional) -->
    <script>
        window.onafterprint = function() {
            // window.close(); // Uncomment if needed
        };
    </script>
</body>
</html>