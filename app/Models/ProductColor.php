<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductColor extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'shortcut',
    ];

    protected $casts = [
        'code' => 'string',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_color_id');
    }
}


