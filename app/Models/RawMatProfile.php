<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMatProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'gsm',
        'width_size',
        'classification',
        'supplier',
        'country_origin',
    ];

    public function rawmatOrders(): HasMany
    {
        return $this->hasMany(RawMatOrder::class);
    }


}
