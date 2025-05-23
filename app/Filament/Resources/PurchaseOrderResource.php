<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;
    protected static ?string $navigationGroup = 'Purchasing';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('po_number')
                ->label('PO Number')
                ->disabled()
                ->dehydrated(false),

            Select::make('purchase_requisition_id')
                ->label('Purchase Requisition')
                ->searchable()
                ->required()
                ->reactive()
                ->options(function () {
                    $used = PurchaseOrder::pluck('purchase_requisition_id')->toArray();
                    return PurchaseRequisition::whereNotIn('id', $used)
                        ->get()
                        ->mapWithKeys(fn ($pr) => [$pr->id => "Date: {$pr->created_at}"]);
                })
                ->afterStateUpdated(function ($state, callable $set) {
                    $pr = PurchaseRequisition::with('items.item')->find($state);
                    if ($pr) {
                        $unitPrices = [];
                        foreach ($pr->items as $item) {
                            $unitPrices[$item->item->id] = null;
                        }
                        $set('unit_prices', $unitPrices);
                    } else {
                        $set('unit_prices', []);
                    }
                }),

            Select::make('supplier_id')
                ->label('Supplier')
                ->options(fn () => Supplier::where('status', 'active')->pluck('name', 'id'))
                ->searchable()
                ->required(),

            DatePicker::make('order_date')
                ->label('Order Date')
                ->required(),

                Placeholder::make('items_preview')
    ->label('Items')
    ->content(function ($get) {
        $prId = $get('purchase_requisition_id');
        if (!$prId) return new HtmlString('No PR selected.');

        $pr = \App\Models\PurchaseRequisition::with('items.item')->find($prId);
        if (!$pr || $pr->items->isEmpty()) return new HtmlString('No items found.');

        $html = "<table class='table-auto text-sm w-full border border-gray-300'>";
        $html .= "<thead><tr>
                    <th class='px-2 py-1 text-left border-b'>Item ID</th>
                    <th class='px-2 py-1 text-left border-b'>Item Name</th>
                    <th class='px-2 py-1 text-left border-b'>Quantity</th>
                  </tr></thead><tbody>";

        foreach ($pr->items as $item) {
            $html .= "<tr>
                        <td class='px-2 py-1 border-b'>{$item->item->id}</td>
                        <td class='px-2 py-1 border-b'>{$item->item->name}</td>
                        <td class='px-2 py-1 border-b'>{$item->quantity}</td>
                      </tr>";
        }

        $html .= "</tbody></table>";
        return new HtmlString($html);
    })
    ->columnSpanFull(),

    KeyValue::make('unit_prices')
    ->label('Harga per Item')
    ->keyLabel('Item ID')
    ->valueLabel('Harga (Rp)')
    ->required()
    ->disableEditingKeys()
    ->reactive()
    ->afterStateHydrated(function ($state, callable $set, $get, $record) {
        if (!$record) return;
        
        $unitPrices = [];
        // Ambil data dari relasi purchaseOrderItems
        foreach ($record->purchaseOrderItems as $poItem) {
            $unitPrices[$poItem->item_id] = $poItem->unit_price; // sesuaikan nama kolom harga
        }
        
        $set('unit_prices', $unitPrices);
    })
    ->afterStateUpdated(function ($state, callable $set, callable $get) {
        // Optional: update total saat harga berubah
        $prId = $get('purchase_requisition_id');
        if (!$prId) {
            $set('grand_total', 0);
            return;
        }
        $pr = \App\Models\PurchaseRequisition::with('items')->find($prId);
        if (!$pr) {
            $set('grand_total', 0);
            return;
        }

        $total = 0;
        foreach ($state as $itemId => $price) {
            $quantity = $pr->items->firstWhere('item_id', $itemId)?->quantity ?? 0;
            $total += $quantity * $price;
        }
        $set('grand_total', $total);
    })

    ->columnSpanFull(),

    TextInput::make('grand_total')
    ->label('Total Keseluruhan')
    ->prefix('Rp')
    ->disabled()
    ->dehydrated(false)
    ->afterStateHydrated(function ($state, callable $set, $get, $record) {
        if (!$record) return;
        $total = 0;
        foreach ($record->purchaseOrderItems as $poItem) {
            $total += $poItem->unit_price * $poItem->quantity;
        }
        $set('grand_total', $total);
    }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('po_number')->label('PO Number')->sortable()->searchable(),
                TextColumn::make('order_date')->date()->sortable(),
                TextColumn::make('supplier.name')->label('Supplier')->sortable(),
                TextColumn::make('status')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->visible(fn ($record) => $record->status === 'draft'),
        
            Action::make('mark_ordered')
                ->label('Mark as Ordered')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn ($record) => 
                    auth()->user()?->hasRole('director') && $record->status === 'draft'
                )
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['status' => 'ordered'])),
        
            Action::make('mark_received')
                ->label('Processed')
                ->icon('heroicon-o-truck')
                ->color('success')
                ->visible(fn ($record) => 
                    auth()->user()?->hasRole('supplier') && $record->status === 'ordered'
                )
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['status' => 'received'])),
        
            Action::make('mark_canceled')
                ->label('Rejected')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn ($record) => 
                    auth()->user()?->hasRole('supplier') && $record->status === 'ordered'
                )
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['status' => 'canceled'])),
        
            Action::make('preview')
                ->label('Preview PDF')
                ->url(fn ($record) => route('purchase-orders.preview', $record))
                ->icon('heroicon-o-eye')
                ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
