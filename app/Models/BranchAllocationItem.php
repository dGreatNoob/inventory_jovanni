<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchAllocationItem extends Model
    {
        protected $fillable = [
            'branch_allocation_id',
            'product_id',
            'quantity',
            'scanned_quantity',
            'unit_price',
            'box_id',
            'delivery_receipt_id',
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

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function deliveryReceipt()
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }
}