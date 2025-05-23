<?php

namespace App\Http\Controllers;

use App\Models\CashDisbursementReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExpenditureReportPdfController extends Controller
{
    //
    public function __invoke(CashDisbursementReport $record)
    {
        $payments = $record->payments()
            ->with('purchaseReceipt.purchaseOrder.purchaseOrderItems.item')
            ->get();

        $pdf =Pdf::loadView('pdf.ExpenditureReport', [
            'record' => $record,
            'payments' => $payments,
        ]);

        return $pdf->stream("expenditure-report-{$record->id}.pdf");
    }
}
