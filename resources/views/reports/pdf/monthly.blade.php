<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan - {{ $monthName }} {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
        }
        .summary {
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MINIMARKET JAYUSMAN</h1>
        <p>Laporan Transaksi Bulanan</p>
        <p>{{ $monthName }} {{ $year }}</p>
    </div>

    <div class="summary">
        <p>Total Transaksi: {{ number_format($data->sum('Number of Transactions')) }}</p>
        <p>Total Pendapatan: Rp {{ number_format($data->sum('Total Revenue'), 0, ',', '.') }}</p>
        <p>Rata-rata Harian: Rp {{ number_format($data->avg('Total Revenue'), 0, ',', '.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th class="text-right">Jumlah Transaksi</th>
                <th class="text-right">Total Pendapatan</th>
                <th class="text-right">Rata-rata Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row['Date'] }}</td>
                <td class="text-right">{{ number_format($row['Number of Transactions']) }}</td>
                <td class="text-right">Rp {{ number_format($row['Total Revenue'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($row['Average Transaction'], 0, ',', '.') }}</td>
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