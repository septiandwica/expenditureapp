<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pastikan purchase_receipt_id tetap diisi agar Select bisa resolve opsi dengan benar
        $data['purchase_receipt_id'] = $this->record->purchase_receipt_id;

        return $data;
    }
}
