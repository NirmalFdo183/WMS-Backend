<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'invoice_date',
        'total_bill_amount',
        'discount',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function batchStocks()
    {
        return $this->hasMany(Batch_Stock::class, 'supplier_invoice_id');
    }
}
