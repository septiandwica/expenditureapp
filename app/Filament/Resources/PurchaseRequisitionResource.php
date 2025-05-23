<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseRequisitionResource\Pages;
use App\Filament\Resources\PurchaseRequisitionResource\RelationManagers;
use App\Models\PurchaseRequisition;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PurchaseRequisitionResource extends Resource
{
    protected static ?string $model = PurchaseRequisition::class;
    protected static ?string $navigationGroup = 'Purchasing';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationLabel = 'Purchase Requisitions';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->maxLength(500),

                Repeater::make('items')
                    ->relationship('items')
                    ->label('Daftar Barang')
                    ->schema([
                        Select::make('item_id')
                            ->label('Barang')
                            ->relationship('item', 'name')
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        TextInput::make('note')
                            ->label('Catatan')
                            ->maxLength(255)
                            ->nullable(),
                    ])
                    ->minItems(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('description')->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('requested_by.name')->label('Diajukan Oleh'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                  Action::make('submit')
                  ->label('Submit')
                  ->icon('heroicon-o-paper-airplane')
                  ->visible(fn ($record) => 
                      in_array($record->status, ['draft', 'rejected']) &&
                      auth()->user()?->hasRole('production')
                  )
                  ->requiresConfirmation()
                  ->action(function ($record) {
                      $record->status = 'submitted';
                      $record->save();
                  }),

Action::make('accepted')
    ->label('Approve')
    ->icon('heroicon-o-check-circle')
    ->color('success')
    ->visible(fn ($record) => 
        auth()->user()?->hasRole('director') && $record->status === 'submitted'
    )
    ->requiresConfirmation()
    ->action(function ($record) {
        $record->status = 'accepted';
        $record->save();
    }),

Action::make('reject')
    ->label('Reject')
    ->icon('heroicon-o-x-circle')
    ->color('danger')
    ->visible(fn ($record) => 
        auth()->user()?->hasRole('director') && $record->status === 'submitted'
    )
    ->requiresConfirmation()
    ->action(function ($record) {
        $record->status = 'rejected';
        $record->save();
    }),
    Tables\Actions\EditAction::make()
    ->visible(fn ($record) => $record->status !== 'accepted'),

Tables\Actions\ViewAction::make(), 
                Action::make('Download PDF')
                ->label('Preview PDF')
                ->url(fn ($record) => route('purchase-requisition.pdf.preview', $record->id)) 
                ->openUrlInNewTab()
                ->icon('heroicon-o-eye'),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseRequisitions::route('/'),
            'create' => Pages\CreatePurchaseRequisition::route('/create'),
            'edit' => Pages\EditPurchaseRequisition::route('/{record}/edit'),
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
