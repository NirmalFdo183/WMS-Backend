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
        'driver_id',
        'helper_id',
        'cash_collector_id',
        'sales_rep_id',
    ];

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function helper()
    {
        return $this->belongsTo(Employee::class, 'helper_id');
    }

    public function cashCollector()
    {
        return $this->belongsTo(Employee::class, 'cash_collector_id');
    }

    public function salesRep()
    {
        return $this->belongsTo(SalesRep::class);
    }

    public function loadingItems()
    {
        return $this->hasMany(LoadListItem::class);
    }
}
