<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderPdfController extends Controller
{
    public function preview(PurchaseOrder $record)
    {
        $record->load(['supplier', 'purchaseRequisition.items.item', 'purchaseOrderItems.item']);
    
        $pdf = Pdf::loadView('pdf.purchase-order', compact('record'));
    
        return $pdf->stream("PO_{$record->po_number}.pdf");
    }
}

