<?php

namespace App\Filament\Resources\CashDisbursementReportResource\Pages;

use App\Filament\Resources\CashDisbursementReportResource;
use App\Models\Payment;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCashDisbursementReport extends CreateRecord
{
    protected static string $resource = CashDisbursementReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
{
    // Buang payments_preview karena hanya untuk tampilan
    unset($data['payments_preview_data']);

    // Tambahkan reported_by dari user yang login
    $data['reported_by'] = Auth::id();

    return $data;
}

protected function afterCreate(): void
{
    $from = $this->data['from_date'];
    $to = $this->data['to_date'];

    $paymentIds = Payment::whereBetween('payment_date', [$from, $to])->pluck('id');

    $this->record->payments()->sync($paymentIds);
}


}
