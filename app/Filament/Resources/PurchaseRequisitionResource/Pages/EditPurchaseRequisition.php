<?php

namespace App\Filament\Resources\PurchaseRequisitionResource\Pages;

use App\Filament\Resources\PurchaseRequisitionResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseRequisition extends EditRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    public function mount($record): void
{
    parent::mount($record);

    if ($this->record->status === 'accepted') {
        Notification::make()
            ->title('Data tidak dapat diubah karena sudah Accepted')
            ->danger()
            ->send();

        $this->redirect(static::getResource()::getUrl('index'));
    }
}
}
