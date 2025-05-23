<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashDisbursementReportResource\Pages;
use App\Filament\Resources\CashDisbursementReportResource\RelationManagers;
use App\Models\CashDisbursementReport;
use App\Models\Payment;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CashDisbursementReportResource extends Resource 
{
    protected static ?string $model = CashDisbursementReport::class;
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?string $navigationLabel = 'Expenditure Report';

    protected static ?string $navigationIcon = 'heroicon-o-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                DatePicker::make('from_date')
                ->label('From Date')
                ->required()
                ->reactive()
                ->afterStateUpdated(fn($state, callable $get, callable $set) => self::updatePayments($get, $set)),

            DatePicker::make('to_date')
                ->label('To Date')
                ->required()
                ->reactive()
                ->afterStateUpdated(fn($state, callable $get, callable $set) => self::updatePayments($get, $set)),

            Textarea::make('description')
                ->required(),

            TextInput::make('amount')
                ->numeric()
                ->required()
                ->readOnly(),

            Hidden::make('payments_preview_data'),

            Repeater::make('payments_preview_data')
                ->label('Payments')
                ->schema([
                    TextInput::make('receipt')->label('Receipt')->readOnly(),
                    TextInput::make('method')->label('Method')->readOnly(),
                    TextInput::make('amount')->label('Amount')->readOnly(),
                ])
                ->columns(3)
                ->disabled()
                ->visible(fn(callable $get) => filled($get('payments_preview_data'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('No')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('from_date')
                    ->label('Dari Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('to_date')
                    ->label('Sampai Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Total')
                    ->money('idr')
                    ->sortable()
                    ->alignRight(),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('reportedBy.name')
                    ->label('Dilaporkan Oleh')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('preview')
                ->label('Preview PDF')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => route('filament.resources.cash-disbursement-reports.pdf', $record), true)
                ->openUrlInNewTab()
    
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function updatePayments(callable $get, callable $set): void
{
    $from = $get('from_date');
    $to = $get('to_date');

    if (!($from && $to)) {
        $set('payments_preview_data', []);
        $set('amount', null);
        return;
    }

    $payments = Payment::with('purchaseReceipt')
        ->whereBetween('payment_date', [$from, $to])
        ->get();

    $set('payments_preview_data', $payments->map(fn ($p) => [
        'receipt' => $p->purchaseReceipt->receipt_number ?? '-',
        'method' => $p->payment_method,
        'amount' => number_format($p->amount, 2),
    ])->toArray());

    $set('amount', $payments->sum('amount'));
}
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashDisbursementReports::route('/'),
            'create' => Pages\CreateCashDisbursementReport::route('/create'),
            'edit' => Pages\EditCashDisbursementReport::route('/{record}/edit'),
        ];
    }
   
}
