<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'inventory_movements';

    protected $fillable = [
        'product_id',
        'location_id',
        'movement_type',
        'quantity',
        'unit_cost',
        'total_cost',
        'currency_id',
        'exchange_rate_applied',
        'unit_cost_original',
        'reference_type',
        'reference_id',
        'notes',
        'metadata',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'exchange_rate_applied' => 'decimal:6',
        'unit_cost_original' => 'decimal:2',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    // Scopes
    public function scopeStockIn($query)
    {
        return $query->whereIn('movement_type', ['purchase', 'return', 'transfer_in', 'adjustment'])
                    ->where('quantity', '>', 0);
    }

    public function scopeStockOut($query)
    {
        return $query->whereIn('movement_type', ['sale', 'transfer_out', 'damage', 'theft', 'expired'])
                    ->where('quantity', '<', 0);
    }

    public function scopeByMovementType($query, $type)
    {
        return $query->where('movement_type', $type);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getMovementDirectionAttribute(): string
    {
        return $this->quantity > 0 ? 'in' : 'out';
    }

    public function getAbsoluteQuantityAttribute(): float
    {
        return abs($this->quantity);
    }

    public function getFormattedQuantityAttribute(): string
    {
        $direction = $this->movement_direction === 'in' ? '+' : '-';
        return $direction . number_format($this->absolute_quantity, 2);
    }

    public function getMovementTypeLabelAttribute(): string
    {
        return match($this->movement_type) {
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'return' => 'Return',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out',
            'adjustment' => 'Adjustment',
            'damage' => 'Damage',
            'theft' => 'Theft',
            'expired' => 'Expired',
            default => 'Unknown'
        };
    }

    public function getMovementTypeColorAttribute(): string
    {
        return match($this->movement_type) {
            'purchase', 'return', 'transfer_in', 'adjustment' => 'green',
            'sale', 'transfer_out' => 'blue',
            'damage', 'theft', 'expired' => 'red',
            default => 'gray'
        };
    }

    // Methods
    public function calculateTotalCost(): float
    {
        return $this->absolute_quantity * ($this->unit_cost ?? 0);
    }
}