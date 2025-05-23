<?php

namespace App\Filament\Resources\CashDisbursementReportResource\Pages;

use App\Filament\Resources\CashDisbursementReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashDisbursementReports extends ListRecords
{    
    protected static string $resource = CashDisbursementReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
