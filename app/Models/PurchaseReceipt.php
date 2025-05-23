<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReceipt extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'supplier_id',
        'receipt_number',
        'received_date',
        'total_amount',
        'discount',       // pastikan ada ini
        'net_total',      // pastikan ada ini jika kamu simpan net_total di DB
        'is_verified',
        'verified_at',
        'notes',
        'attachment',
    ];

    protected static function booted()
{
    static::creating(function ($receipt) {
        $receipt->receipt_number = self::generateReceiptNumber();
    });
    static::saving(function ($model) {
        $model->net_total = $model->total_amount - ($model->total_amount * ($model->discount / 100));
    });
}

public static function generateReceiptNumber()
{
    $date = now()->format('Ymd'); // contoh: 20250518
    $lastId = self::max('id') + 1;
    return 'RCPT-' . $date . '-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
}
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments()
{
    return $this->hasMany(Payment::class);
}
}
