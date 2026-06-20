<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadingReturn extends Model
{
    protected $fillable = [
        'loading_id',
        'batch_id',
        'qty',
        'return_date',
        'reason',
    ];

    public function loading()
    {
        return $this->belongsTo(Loading::class);
    }

    public function batchStock()
    {
        return $this->belongsTo(Batch_Stock::class, 'batch_id');
    }
}
