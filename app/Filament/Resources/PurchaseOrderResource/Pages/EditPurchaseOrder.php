<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['purchase_requisition_id']) && isset($data['unit_prices'])) {
            $pr = \App\Models\PurchaseRequisition::with('items')->find($data['purchase_requisition_id']);
            $total = 0;
            if ($pr) {
                foreach ($data['unit_prices'] as $itemId => $price) {
                    $quantity = $pr->items->firstWhere('item_id', $itemId)?->quantity ?? 0;
                    $total += $quantity * $price;
                }
            }
            $data['grand_total'] = $total;
        }
        return $data;
    }
    
    protected function afterSave(): void
{
    $purchaseOrder = $this->record;
    $data = $this->form->getState();

    if (isset($data['unit_prices']) && is_array($data['unit_prices'])) {
        foreach ($data['unit_prices'] as $itemId => $price) {
            $poItem = $purchaseOrder->purchaseOrderItems()->where('item_id', $itemId)->first();
            if ($poItem) {
                $quantity = $poItem->quantity; // ambil quantity dari database
                $poItem->unit_price = $price;
                $poItem->total_price = $quantity * $price;  // update total price per item
                $poItem->save();
            }
        }
    }

    if (isset($data['grand_total'])) {
        $purchaseOrder->grand_total = $data['grand_total'];
        $purchaseOrder->save();
    }
}

    
    
    
}
