<?php

namespace App\Filament\Resources\PurchaseReceiptResource\Pages;

use App\Filament\Resources\PurchaseReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseReceipt extends EditRecord
{
    protected static string $resource = PurchaseReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
}
