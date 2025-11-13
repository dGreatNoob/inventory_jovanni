<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'startDate',
        'endDate',
        'branch',
        'product',
        'second_product', // <-- add this
        'type',
    ];

    protected $casts = [
        'startDate' => 'date',
        'endDate' => 'date',
        'branch' => 'array',
        'product' => 'array',
        'second_product' => 'array', // <-- add this
    ];

    // ✅ Get Branch Names
    public function getBranchNamesAttribute()
    {
        $branchIds = json_decode($this->branch, true) ?? [];
        if (!is_array($branchIds)) $branchIds = [];
        return \App\Models\Branch::whereIn('id', $branchIds)->pluck('name')->toArray();
    }

    // ✅ Get Product Names
    public function getProductNamesAttribute()
    {
        $productIds = json_decode($this->product, true) ?? [];
        if (!is_array($productIds)) $productIds = [];
        return \App\Models\Product::whereIn('id', $productIds)->pluck('name')->toArray();
    }

    // ✅ Get Second Product Names
    public function getSecondProductNamesAttribute()
    {
        $secondIds = json_decode($this->second_product, true) ?? [];
        if (!is_array($secondIds)) $secondIds = [];
        return \App\Models\Product::whereIn('id', $secondIds)->pluck('name')->toArray();
    }
}
