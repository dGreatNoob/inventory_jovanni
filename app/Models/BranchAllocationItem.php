<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchAllocationItem extends Model
{
    protected $fillable = [
        'branch_allocation_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    public function branchAllocation()
    {
        return $this->belongsTo(BranchAllocation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}