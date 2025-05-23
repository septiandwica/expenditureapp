<?php

namespace App\Filament\Widgets;

use App\Models\CashDisbursementReport;
use Filament\Widgets\ChartWidget;

class TopDisbursementsChart extends ChartWidget
{
    protected static ?string $heading = 'Expenditure Report (period)';
    protected static ?int $sort = 1;
    protected static string $color = 'info';

    protected function getData(): array
    {
        $top = CashDisbursementReport::orderBy('amount', 'desc')
            ->take(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Amount',
                    'data' => $top->pluck('amount')->toArray(),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $top->map(fn($r) => 'ID: '.$r->id)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
