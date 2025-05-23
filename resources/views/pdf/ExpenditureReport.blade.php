<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Expenditure Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; }
        .header { display: flex; align-items: center; margin-bottom: 20px; }
        .header img { height: 60px; margin-right: 15px; }
        .company-name { font-size: 20px; font-weight: bold; }
        .title { font-size: 16px; margin-bottom: 5px; }
        .period { font-size: 14px; margin-bottom: 15px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        <div class="company-name">TOCOCO INDONESIA</div>
    </div>

    <div class="title">EXPENDITURE REPORT</div>
    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($record->from_date)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($record->to_date)->format('d M Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Receipt Number</th>
                <th>Month</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
        
            @foreach($payments as $payment)
                @php
                    $receipt = $payment->purchaseReceipt;
                    $receiptAmount = $receipt?->net_total ?? 0;
                    $total += $receiptAmount;
        
                    $poItems = $receipt?->purchaseOrder?->purchaseOrderItems ?? collect();
                    $itemNames = $poItems->pluck('item.name')->filter()->implode(', ');
                @endphp
        
                @if($receipt && $poItems->count())
                    <tr>
                        <td>{{ $receipt->receipt_number ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('F') }}</td>
                        <td>{{ $itemNames }}</td>
                        <td>Rp. {{ number_format($receiptAmount, 2, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
        
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td>Rp. {{ number_format($total, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
