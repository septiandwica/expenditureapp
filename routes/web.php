<?php

use App\Http\Controllers\ExpenditureReportPdfController;
use App\Http\Controllers\PaymentPdfController;
use App\Http\Controllers\PurchaseOrderPdfController;
use App\Http\Controllers\PurchaseReceiptPdfController;
use App\Http\Controllers\PurchaseRequisitionPdfController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});


Route::get('/filament/resources/cash-disbursement-reports/{record}/pdf', ExpenditureReportPdfController::class)
    ->name('filament.resources.cash-disbursement-reports.pdf');

Route::get('/purchase-orders/{record}/preview', [PurchaseOrderPdfController::class, 'preview'])
    ->name('purchase-orders.preview');

Route::get('/purchase-receipts/{record}/preview', [PurchaseReceiptPdfController::class, 'preview'])
    ->name('purchase-receipts.preview');

Route::get('/purchase-requisition/{id}/pdf', [PurchaseRequisitionPdfController::class, 'preview'])->name('purchase-requisition.pdf.preview');
    
Route::get('/payments/{payment}/preview-pdf', [PaymentPdfController::class, 'preview'])
    ->name('payments.preview-pdf');
require __DIR__.'/auth.php';
