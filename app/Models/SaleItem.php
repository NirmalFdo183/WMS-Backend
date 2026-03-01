<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'batch_id',
        'product_id',
        'qty',
        'unit_price',
        'total',
        'discount',
    ];

    /**
     * Get the sale that owns the item.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the product associated with the sale item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the batch associated with the sale item.
     */
    public function batchStock()
    {
        return $this->belongsTo(Batch_Stock::class, 'batch_id');
    }
}
