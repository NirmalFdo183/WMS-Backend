<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch_Stock extends Model
{
    protected $table = 'batch__stocks';

    protected $fillable = [
        'product_id',
        'supplier_invoice_id',
        'no_cases',
        'pack_size',
        'qty',
        'retail_price',
        'netprice',
        'expiry_date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_invoice_id');
    }
}
