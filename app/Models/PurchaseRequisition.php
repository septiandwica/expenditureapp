<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequisition extends Model
{
    //
    use HasFactory, SoftDeletes;
    protected $with = ['items'];
    protected $fillable = ['requested_by_id', 'status', 'description'];

    public function items()
    {
        return $this->hasMany(PRItem::class);
    }


    public function requested_by()
{
    return $this->belongsTo(User::class, 'requested_by_id');
}
    

}
