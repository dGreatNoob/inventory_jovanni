<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchAllocationItem extends Model
    {
        protected $fillable = [
        'branch_allocation_id',
        'product_id',
        'quantity',
        'scanned_quantity', // Add this
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'scanned_quantity' => 'integer',
        'unit_price' => 'decimal:2',
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