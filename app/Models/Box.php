<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    protected $fillable = [
        'branch_allocation_id',
        'box_number',
        'status',
        'current_count',
    ];

    protected $casts = [
        'current_count' => 'integer',
    ];

    public function branchAllocation()
    {
        return $this->belongsTo(BranchAllocation::class);
    }

    public function deliveryReceipts()
    {
        return $this->hasMany(DeliveryReceipt::class);
    }

    public function allocationItems()
    {
        return $this->hasMany(BranchAllocationItem::class);
    }

}
