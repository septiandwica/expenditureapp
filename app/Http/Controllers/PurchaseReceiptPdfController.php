<?php


namespace App\Http\Controllers;

use App\Models\PurchaseReceipt;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseReceiptPdfController extends Controller
{
    public function preview(PurchaseReceipt $record)
    {
        $record->load([
            'purchaseOrder.supplier',
            'purchaseOrder.items.item',
        ]);
    
        $pdf = Pdf::loadView('pdf.purchase-receipt', compact('record'));
    
        return $pdf->stream("Receipt_{$record->receipt_number}.pdf");
    }
    
}
