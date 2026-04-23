<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loading extends Model
{
    protected $fillable = [
        'load_number',
        'truck_id',
        'route_id',
        'prepared_date',
        'loading_date',
        'status',
    ];
}
