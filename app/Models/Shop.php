<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'shop_code',
        'shop_name',
        'Address',
        'phoneno',
        'route_code',
    ];

    public function route()
    {
        return $this->belongsTo(Routes::class, 'route_code', 'route_code');
    }
}
