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

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
