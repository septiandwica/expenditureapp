<!DOCTYPE html>
<html>
<head>
    <title>Payment #{{ $payment->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px;}
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left;}
    </style>
</head>
<body>
    <h1>Payment Details #{{ $payment->id }}</h1>

    <p><strong>Receipt Number:</strong> {{ $payment->purchaseReceipt->receipt_number }}</p>
    <p><strong>Amount:</strong> IDR {{ number_format($payment->amount, 0, ',', '.') }}</p>
    <p><strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</p>
    <p><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
    <p><strong>Notes:</strong> {{ $payment->notes }}</p>

</body>
</html>
