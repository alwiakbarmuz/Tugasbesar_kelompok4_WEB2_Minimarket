<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok - {{ date('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
        }
        .low-stock {
            color: #e67e22;
            font-weight: bold;
        }
        .out-stock {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MINIMARKET JAYUSMAN</h1>
        <p>Laporan Stok Produk</p>
        <p>{{ date('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Barcode</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="text-center">Stok</th>
                <th class="text-right">Harga Jual</th>
                <th class="text-right">Nilai Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row['Barcode'] }}</td>
                <td>{{ $row['Product Name'] }}</td>
                <td>{{ $row['Category'] }}</td>
                <td class="text-center">{{ number_format($row['Current Stock']) }}</td>
                <td class="text-right">Rp {{ number_format($row['Selling Price'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($row['Stock Value'], 0, ',', '.') }}</td>
                <td class="{{ $row['Stock Status'] == 'Habis' ? 'out-stock' : ($row['Stock Status'] == 'Menipis' ? 'low-stock' : '') }}">
                    {{ $row['Stock Status'] }}
                </td>
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