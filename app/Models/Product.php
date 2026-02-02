<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'material_code',
        'name',
        'category',
    ];

    public function batchStocks()
    {
        return $this->hasMany(Batch_Stock::class);
    }
}
