<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    protected $fillable = ['name', 'unit', 'description'];

    // Relasi ke PRItem (optional)
    public function prItems()
    {
        return $this->hasMany(PRItem::class);
    }
}
