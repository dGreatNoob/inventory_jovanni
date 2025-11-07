<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReceipt extends Model
{
    protected $fillable = [
        'batch_allocation_id',
        'branch_id',
        'status',
        'date_received',
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
        return $this->hasMany(SalesReceiptItem::class);
    }
}