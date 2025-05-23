<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDisbursementReport extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'reported_by',
        'from_date',
        'to_date',
        'description',
        'amount',
    ];
    public function payments()
{
    return $this->belongsToMany(Payment::class, 'cash_disbursement_report_payments');
}
    
    public function reportedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'reported_by');
    }

    
}
