<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    protected $fillable = [
        'transaction_date',
        'remarks',
        'auto_ref_no',
        'status',
    ];
}
