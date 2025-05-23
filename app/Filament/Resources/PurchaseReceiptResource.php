<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseReceiptResource\Pages;
use App\Filament\Resources\PurchaseReceiptResource\RelationManagers;
use App\Models\PurchaseReceipt;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseReceiptResource extends Resource
{
    protected static ?string $model = PurchaseReceipt::class;
    protected static ?string $navigationGroup = 'Purchasing';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            
            Select::make('purchase_order_id')
            ->label('Purchase Order')
            ->required()
            ->searchable()
            ->relationship('purchaseOrder', 'po_number')
            ->required()
            ->disabled(fn (?PurchaseReceipt $record) => $record !== null) // disable saat edit
            ->dehydrated(fn (?PurchaseReceipt $record) => $record === null) // agar tidak update saat edit
            ->live()
            ->options(function () {
                $usedPoIds = \App\Models\PurchaseReceipt::pluck('purchase_order_id')->toArray();
                return \App\Models\PurchaseOrder::whereNotIn('id', $usedPoIds)
                    ->where('status', 'received') // Tambahkan filter status di sini
                    ->pluck('po_number', 'id');
            })
            ->afterStateUpdated(function ($state, callable $set) {
                $po = \App\Models\PurchaseOrder::with('items.item')->find($state);
                if ($po) {
                    $set('supplier_id', $po->supplier_id);
                    $set('total_amount', $po->items->sum(fn($item) => $item->quantity * $item->unit_price));
                    $set('items', $po->items->map(function ($orderItem) {
                        return [
                            'name' => $orderItem->item->name ?? 'N/A',
                            'quantity' => $orderItem->quantity,
                            'unit_price' => $orderItem->unit_price,
                            'subtotal' => $orderItem->quantity * $orderItem->unit_price,
                        ];
                    })->toArray());
                } else {
                    $set('supplier_id', null);
                    $set('total_amount', 0);
                    $set('items', []);
                }
            }),

            Select::make('supplier_id')
                ->relationship('supplier', 'name')
                ->required()
                ->disabled()
                ->dehydrated(), // tetap tersimpan walau disabled

            TextInput::make('receipt_number')
                ->disabled()
                ->dehydrated(),

            DatePicker::make('received_date')
                ->required(),

            Repeater::make('items')
                ->label('Items Ordered')
                ->disabled()
                ->schema([
                    TextInput::make('name')->label('Item Name')->disabled(),
                    TextInput::make('quantity')->label('Qty')->disabled(),
                    TextInput::make('unit_price')->label('Price')->disabled(),
                    TextInput::make('subtotal')->label('Subtotal')->disabled(),
                ])
                ->default([])
                ->columnSpanFull(),

            TextInput::make('total_amount')
                ->label('Total Amount')
                ->disabled()
                ->dehydrated(),

                TextInput::make('discount')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->maxValue(100)
                ->reactive(),

            \Filament\Forms\Components\Placeholder::make('net_total')
    ->label('Net Total')
    ->content(function ($get) {
        $total = (float) ($get('total_amount') ?? 0);
        $discountPercent = (float) ($get('discount') ?? 0);
    
        $discountNominal = ($discountPercent / 100) * $total;
        $netTotal = $total - $discountNominal;
    
        return 'Rp ' . number_format($netTotal, 0, ',', '.');
    }),

            Toggle::make('is_verified'),

            DatePicker::make('verified_at')
                ->visible(fn ($get) => $get('is_verified')),

            Textarea::make('notes'),

            FileUpload::make('attachment')
                ->directory('receipts')
                ->label('Upload Nota Pembelian')
                ->preserveFilenames()
                ->acceptedFileTypes(['application/pdf', 'image/*']),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('Nomor Penerimaan')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('purchaseOrder.po_number')
                    ->label('Nomor PO')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('received_date')
                    ->label('Tanggal Penerimaan')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('purchaseOrder.supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('net_total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Status Verifikasi')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('supplier')
                    ->relationship('supplier', 'name')
                    ->label('Supplier'),
                    
                Tables\Filters\Filter::make('received_date')
                    ->form([
                        Forms\Components\DatePicker::make('received_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('received_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['received_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('received_date', '>=', $date),
                            )
                            ->when(
                                $data['received_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('received_date', '<=', $date),
                            );
                    }),
                    
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Status Verifikasi')
                    ->placeholder('Semua')
                    ->trueLabel('Terverifikasi')
                    ->falseLabel('Belum Terverifikasi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('Preview PDF')
                ->label('Preview PDF')
        ->icon('heroicon-o-eye')
        ->url(fn ($record) => route('purchase-receipts.preview', $record))
        ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('received_date', 'desc');
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
            'index' => Pages\ListPurchaseReceipts::route('/'),
            'create' => Pages\CreatePurchaseReceipt::route('/create'),
            'edit' => Pages\EditPurchaseReceipt::route('/{record}/edit'),
        ];
    }
}
