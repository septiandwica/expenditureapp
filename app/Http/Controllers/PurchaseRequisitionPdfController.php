<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PurchaseRequisitionPdfController extends Controller
{
    public function preview($id)
    {
        $record = PurchaseRequisition::with(['items.item', 'requested_by'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.purchase-requisition', compact('record'))->setPaper('a4', 'portrait');

        return $pdf->stream("purchase-requisition-{$id}.pdf");
    }

    // public function download($id)
    // {
    //     $record = PurchaseRequisition::with(['items.item', 'requested_by'])->findOrFail($id);

    //     $pdf = Pdf::loadView('pdf.purchase-requisition', compact('record'))->setPaper('a4', 'portrait');

    //     return $pdf->download("purchase-requisition-{$id}.pdf");
    // }
}
