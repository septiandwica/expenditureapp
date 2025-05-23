<?php

namespace App\Filament\Resources;

use App\Models\Payment;
use App\Models\PurchaseReceipt;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationGroup = 'Transactions';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Select::make('purchase_receipt_id')
            ->label('Purchase Receipt')
            ->searchable()
            ->required()
            ->options(function (callable $get) {
                // Jika ada record, berarti edit mode, tampilkan hanya satu pilihan (yang sudah dipakai)
                if ($record = static::getCurrentRecord()) {
                    $pr = PurchaseReceipt::find($record->purchase_receipt_id);
                    if ($pr) {
                        return [$pr->id => "Receipt #{$pr->receipt_number}"];
                    }
                    return [];
                }

                // Jika create mode, tampilkan yang belum pernah dipakai
                $usedIds = Payment::pluck('purchase_receipt_id')->toArray();
                return PurchaseReceipt::whereNotIn('id', $usedIds)
                    ->get()
                    ->mapWithKeys(fn($pr) => [$pr->id => "Receipt #{$pr->receipt_number}"])
                    ->toArray();
            })
            ->reactive()
            ->afterStateUpdated(function ($state, callable $set) {
                $pr = PurchaseReceipt::find($state);
                if ($pr) {
                    $set('amount', $pr->net_total ?? 0);
                } else {
                    $set('amount', 0);
                }
            })
            ->disabled(fn () => static::getCurrentRecord() !== null), 
                DatePicker::make('payment_date')
                ->required(),

            Select::make('payment_method')
                ->options([
                    'transfer' => 'Bank Transfer',
                    'cash' => 'Cash',
                    // bisa tambah metode lain
                ])
                ->required(),

            TextInput::make('amount')
                ->numeric()
                ->required()
                ->minValue(0),

            Textarea::make('notes'),

            FileUpload::make('proof_file')
    ->directory('payments')              // path relatif ke storage/app/public
    ->label('Upload Bukti Pembayaran')
    ->preserveFilenames()
    ->acceptedFileTypes(['application/pdf', 'image/*'])
    ->disk('public')                     // gunakan disk 'public'
    ->visibility('public')
    ->imagePreviewHeight('150')          // preview ukuran
    ->getUploadedFileNameForStorageUsing(fn($file) => $file->getClientOriginalName())  // optional, kalau mau nama asli


        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('purchaseReceipt.receipt_number')->label('Receipt Number')->sortable(),
                TextColumn::make('amount')->label('Amount')->money('idr')->sortable(),
                TextColumn::make('payment_date')->label('Payment Date')->date()->sortable(),
                TextColumn::make('notes')->label('Notes')->limit(30),
                TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('previewPdf')
                ->label('Preview PDF')
                ->url(fn ($record) => route('payments.preview-pdf', $record))
                ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Bisa ditambahkan relasi, misal payment details jika ada
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PaymentResource\Pages\ListPayments::route('/'),
            'create' => \App\Filament\Resources\PaymentResource\Pages\CreatePayment::route('/create'),
            'edit' => \App\Filament\Resources\PaymentResource\Pages\EditPayment::route('/{record}/edit'),
        ];
    }
    protected static function getCurrentRecord()
    {
        // Helper method untuk mengambil record saat ini di form (edit mode)
        // Mengakses request dan route untuk get parameter {record}
        return request()->route('record') ? Payment::find(request()->route('record')) : null;
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
