<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales_rep extends Model
{
    protected $fillable = [
        'rep_id',
        'supplier_id',
        'name',
        'contact',
        'join_date',
        'route_id',
    ];
}
