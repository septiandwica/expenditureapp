<?php

namespace App\Filament\Resources\PurchaseRequisitionResource\Pages;

use App\Filament\Resources\PurchaseRequisitionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;


class CreatePurchaseRequisition extends CreateRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requested_by_id'] = Auth::id();
        return $data;
    }
}
