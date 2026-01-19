<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    protected $fillable = [
        'sales_return_id',
        'branch_allocation_item_id',
        'quantity',
        'reason',
    ];

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function branchAllocationItem()
    {
        return $this->belongsTo(BranchAllocationItem::class);
    }
}