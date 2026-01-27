<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    protected $fillable = [
        'branch_allocation_id',
        'box_id',
        'dr_number',
        'type',
        'parent_dr_id',
        'status',
        'total_items',
        'scanned_items',
    ];

    protected $casts = [
        'total_items' => 'integer',
        'scanned_items' => 'integer',
    ];

    public function branchAllocation()
    {
        return $this->belongsTo(BranchAllocation::class);
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function parentDr()
    {
        return $this->belongsTo(DeliveryReceipt::class, 'parent_dr_id');
    }

    public function childDrs()
    {
        return $this->hasMany(DeliveryReceipt::class, 'parent_dr_id');
    }

    public function allocationItems()
    {
        return $this->hasMany(BranchAllocationItem::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function isMother()
    {
        return $this->type === 'mother';
    }

    public function isCompleted()
    {
        return $this->scanned_items >= $this->total_items;
    }
}
