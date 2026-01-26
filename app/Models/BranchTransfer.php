<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BranchTransfer extends Model
{
    protected $fillable = [
        'transfer_number',
        'source_branch_id',
        'destination_branch_id',
        'status',
        'remarks',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Generate a unique transfer number
     */
    public static function generateTransferNumber(): string
    {
        do {
            $number = 'BT-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('transfer_number', $number)->exists());

        return $number;
    }

    /**
     * Get the source branch
     */
    public function sourceBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'source_branch_id');
    }

    /**
     * Get the destination branch
     */
    public function destinationBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    /**
     * Get the user who created the transfer
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the transfer items
     */
    public function items(): HasMany
    {
        return $this->hasMany(BranchTransferItem::class);
    }

    /**
     * Get total quantity
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get total value
     */
    public function getTotalValueAttribute(): float
    {
        return $this->items->sum('total_value');
    }
}
