<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'purchase_receipt_id',
        'payment_date',
        'payment_method',
        'amount',
        'notes',
        'proof_file',
    ];
    protected static function booted()
    {
        static::creating(function ($payment) {
            $payment->paid_by = $payment->paid_by ?? Auth::id();
        });
    }
    public function purchaseReceipt()
{
    return $this->belongsTo(PurchaseReceipt::class);
}

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function cashDisbursementReports()
    {
        return $this->belongsToMany(CashDisbursementReport::class, 'cash_disbursement_report_payments');
    }

}

