<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Load_list_items extends Model
{
    protected $fillable = [
        'loading_id',
        'batch_id',
        'qty',
        'free_qty',
    ];
}
