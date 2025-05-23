<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'purchase_requisition_id',
        'order_date',
        'status',
    ];
    protected $casts = [
        'unit_prices' => 'array',
    ];
    protected static function booted()
    {
        static::creating(function ($po) {
            $lastId = static::max('id') + 1;
            $date = now()->format('Ymd');
            $po->po_number = 'PO-' . $date . '-' . str_pad($lastId, 3, '0', STR_PAD_LEFT);
        });
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }
    

    // Untuk akses item, langsung dari PR
    // public function items()
    // {
    //     return $this->purchaseRequisition ? $this->purchaseRequisition->items : collect();
    // }
    public function items()
{
    return $this->hasMany(\App\Models\PurchaseOrderItem::class);
}

    public function purchaseOrderItems()
{
    return $this->hasMany(PurchaseOrderItem::class);
}
public function receipt()
{
    return $this->hasOne(PurchaseReceipt::class);
}

}
