<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchTransferItem extends Model
{
    protected $fillable = [
        'branch_transfer_id',
        'product_id',
        'branch_allocation_item_id',
        'quantity',
        'unit_price',
        'total_value',
        'status',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_value' => 'decimal:2',
    ];

    /**
     * Get the branch transfer
     */
    public function branchTransfer(): BelongsTo
    {
        return $this->belongsTo(BranchTransfer::class);
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the branch allocation item
     */
    public function branchAllocationItem(): BelongsTo
    {
        return $this->belongsTo(BranchAllocationItem::class);
    }
}
