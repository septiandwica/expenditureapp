<?php

namespace App\Filament\Resources\PurchaseReceiptResource\Pages;

use App\Filament\Resources\PurchaseReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseReceipts extends ListRecords
{
    protected static string $resource = PurchaseReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
