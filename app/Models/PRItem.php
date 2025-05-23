<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PRItem extends Model
{
    //
    protected $fillable = ['purchase_requisition_id', 'item_id', 'quantity', 'note'];

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    
}

