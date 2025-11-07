<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_number',
        'initial_qty',
        'current_qty',
        'received_date',
        'received_by',
        'location',
        'notes',
    ];

    protected $casts = [
        'received_date' => 'date',
        'initial_qty' => 'decimal:2',
        'current_qty' => 'decimal:2',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Helper methods
    public function isDepleted(): bool
    {
        return $this->current_qty <= 0;
    }

    public function isActive(): bool
    {
        return $this->current_qty > 0;
    }

    public function getUsagePercentageAttribute(): float
    {
        if ($this->initial_qty <= 0) {
            return 0;
        }
        return (($this->initial_qty - $this->current_qty) / $this->initial_qty) * 100;
    }
}