<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesRep extends Model
{
    use HasFactory;

    protected $fillable = [
        'rep_id',
        'supplier_id',
        'name',
        'contact',
        'join_date',
        'route_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function route()
    {
        return $this->belongsTo(\App\Models\Route::class);
    }
}
