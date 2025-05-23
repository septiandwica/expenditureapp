<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Purchase Order</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .info { margin-bottom: 10px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Purchase Order</div>
    </div>

    <div class="info">
        <strong>PO Number:</strong> {{ $record->po_number }}<br>
        <strong>Supplier:</strong> {{ $record->supplier->name }}<br>
        <strong>Order Date:</strong> {{ \Carbon\Carbon::parse($record->order_date)->format('d M Y') }}<br>
        <strong>Status:</strong> {{ ucfirst($record->status) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Unit Price (Rp)</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($record->purchaseOrderItems as $index => $item)
                @php
                    $total = $item->quantity * $item->unit_price;
                    $grandTotal += $total;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->item->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td>{{ number_format($total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td colspan="4" style="text-align: right;">Grand Total</td>
                <td>{{ number_format($grandTotal, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
