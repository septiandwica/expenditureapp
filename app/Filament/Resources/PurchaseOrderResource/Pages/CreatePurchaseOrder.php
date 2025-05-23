<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\PurchaseRequisition;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;
    protected function afterCreate(): void
{
    $data = $this->form->getState();

    $pr = PurchaseRequisition::with('items')->find($data['purchase_requisition_id']);
    $unitPrices = $data['unit_prices'];

    $grandTotal = 0;

    foreach ($pr->items as $prItem) {
        $itemId = $prItem->item_id;
        $quantity = $prItem->quantity;
        $unitPrice = $unitPrices[$itemId] ?? 0;
        $total = $quantity * $unitPrice;
        $grandTotal += $total;

        \App\Models\PurchaseOrderItem::create([
            'purchase_order_id' => $this->record->id,
            'item_id' => $itemId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $total,
        ]);
    }

    $this->record->update([
        'grand_total' => $grandTotal,
    ]);
}



}
