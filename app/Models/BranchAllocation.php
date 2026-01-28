<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchAllocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'batch_allocation_id',
        'branch_id',
        'remarks',
        'status',
    ];

    public function batchAllocation()
    {
        return $this->belongsTo(BatchAllocation::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(BranchAllocationItem::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'branch_allocation_id');
    }
}