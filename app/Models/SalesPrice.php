<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPrice extends Model
{
    protected $fillable = [
        'description',
        'less_percentage',
        'pricing_note',
    ];

    protected $casts = [
        'less_percentage' => 'decimal:2',
    ];
}
