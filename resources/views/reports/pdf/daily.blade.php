<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Harian - {{ $date }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 11px;
        }
        .summary {
            margin-bottom: 20px;
            width: 100%;
        }
        .summary td {
            padding: 8px;
            background: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MINIMARKET JAYUSMAN</h1>
        <p>Laporan Transaksi Harian</p>
        <p>Tanggal: {{ date('d/m/Y', strtotime($date)) }}</p>
    </div>

    <table class="summary">
        <tr>
            <td>Total Transaksi: {{ number_format($data->count()) }}</td>
            <td>Total Pendapatan: Rp {{ number_format($data->sum('Total'), 0, ',', '.') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>Cabang</th>
                <th class="text-right">Total</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row['Invoice Number'] }}</td>
                <td>{{ $row['Date'] }}</td>
                <td>{{ $row['Cashier'] }}</td>
                <td>{{ $row['Branch'] }}</td>
                <td class="text-right">Rp {{ number_format($row['Total'], 0, ',', '.') }}</td>
                <td class="text-center">{{ $row['Status'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>&copy; {{ date('Y') }} Minimarket Jayusman - Sistem Manajemen Terintegrasi</p>
    </div>
</body>
</html>