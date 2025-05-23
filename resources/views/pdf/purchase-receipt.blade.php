<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Receipt - {{ $record->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2, h4 {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
        }

        .section {
            margin-top: 20px;
        }

        .info-table td {
            border: none;
            padding: 4px 0;
        }
    </style>
</head>
<body>

    <h2>Purchase Receipt</h2>
    <p><strong>Receipt Number:</strong> {{ $record->receipt_number }}</p>
    <p><strong>Received Date:</strong> {{ \Carbon\Carbon::parse($record->received_date)->format('d M Y') }}</p>

    <div class="section">
        <h4>Supplier Information</h4>
        <table class="info-table">
            <tr>
                <td><strong>Name:</strong></td>
                <td>{{ $record->purchaseOrder->supplier->name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>{{ $record->purchaseOrder->supplier->email ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td>{{ $record->purchaseOrder->supplier->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Address:</strong></td>
                <td>{{ $record->purchaseOrder->supplier->address ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h4>Items Received</h4>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record->purchaseOrder->items as $item)
                    <tr>
                        <td>{{ $item->item->name ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <table class="info-table">
            <tr>
                <td><strong>Total Amount:</strong></td>
                <td>Rp {{ number_format($record->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Discount:</strong></td>
                <td>{{ $record->discount ?? 0 }}%</td>
            </tr>
            <tr>
                <td><strong>Net Total:</strong></td>
                @php
                    $discount = ($record->discount / 100) * $record->total_amount;
                    $netTotal = $record->total_amount - $discount;
                @endphp
                <td><strong>Rp {{ number_format($netTotal, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    @if ($record->notes)
    <div class="section">
        <h4>Notes</h4>
        <p>{{ $record->notes }}</p>
    </div>
    @endif

</body>
</html>
