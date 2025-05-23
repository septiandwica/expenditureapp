<?php

namespace App\Filament\Resources\CashDisbursementReportResource\Pages;

use App\Filament\Resources\CashDisbursementReportResource;
use App\Models\Payment;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashDisbursementReport extends EditRecord
{
    protected static string $resource = CashDisbursementReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            \Filament\Actions\Action::make('Download PDF')
            ->label('Download PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->url(fn () => route('filament.resources.cash-disbursement-reports.pdf', $this->record))
            ->openUrlInNewTab(),
        ];
    }
    protected function afterSave(): void
    {
        $from = $this->record->from_date;
        $to = $this->record->to_date;

        // Ambil payment IDs berdasarkan tanggal terbaru
        $paymentIds = Payment::whereBetween('payment_date', [$from, $to])->pluck('id');

        // Sync ulang relasi many-to-many
        $this->record->payments()->sync($paymentIds);
    }

}
