<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Purchase Requisition #{{ $record->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 6px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f3f3f3;
        }
        .header {
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Purchase Requisition</h2>
        <p>ID: {{ $record->id }}</p>
        <p>Status: {{ ucfirst($record->status) }}</p>
        <p>Tanggal: {{ \Carbon\Carbon::parse($record->created_at)->format('d M Y') }}</p>
        <p>Diajukan oleh: {{ $record->requested_by->name ?? '-' }}</p>
    </div>

    <h4>Deskripsi</h4>
    <p>{{ $record->description }}</p>

    <h4>Daftar Barang</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($record->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->item->name ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->note ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
