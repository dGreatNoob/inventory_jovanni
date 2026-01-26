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

    // ✅ Get Batch Allocation Ref Nos
    public function getBranchNamesAttribute()
    {
        // For backward compatibility, this still returns branch names if stored as branch IDs
        // But now it primarily returns batch allocation ref_nos
        $batchAllocationIds = json_decode($this->branch, true) ?? [];
        if (!is_array($batchAllocationIds)) $batchAllocationIds = [];
        
        // Check if these are batch allocation IDs (new format) or branch IDs (old format)
        $batchAllocations = \App\Models\BatchAllocation::whereIn('id', $batchAllocationIds)->get();
        if ($batchAllocations->count() > 0) {
            return $batchAllocations->pluck('ref_no')->toArray();
        }
        
        // Fallback to branch names for old data
        return \App\Models\Branch::whereIn('id', $batchAllocationIds)->pluck('name')->toArray();
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
