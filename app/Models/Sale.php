<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_time',
        'user_id',
        'total',
        'status',
        'discount',
        'payment_type',
    ];

    /**
     * Get the items for the sale.
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the user that made the sale.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
