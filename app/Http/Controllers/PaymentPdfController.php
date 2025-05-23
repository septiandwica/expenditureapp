<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf; // pastikan import facade Pdf
use Illuminate\Http\Request;

class PaymentPdfController extends Controller
{
    public function preview(Payment $payment)
    {
        $pdf = Pdf::loadView('pdf.payment', compact('payment'));
        // Bisa langsung return stream supaya langsung tampil di browser
        return $pdf->stream("payment-{$payment->id}.pdf");
    }
}
