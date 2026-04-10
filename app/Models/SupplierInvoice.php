<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    protected $fillable = [
        'supplier_id',
        'total_bill_amount',
        'discount',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
