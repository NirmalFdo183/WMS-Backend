<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'loading_id',
        'batch_id',
        'qty',
        'free_qty',
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
