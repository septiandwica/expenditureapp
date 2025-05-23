<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'status',
        'email',
        'phone',
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}
}
