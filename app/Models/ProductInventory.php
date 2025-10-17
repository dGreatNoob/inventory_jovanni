<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInventory extends Model
{
    use HasFactory;

    protected $table = 'product_inventory';

    protected $fillable = [
        'product_id',
        'location_id',
        'quantity',
        'reserved_quantity',
        'available_quantity',
        'reorder_point',
        'max_stock',
        'last_movement_at'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'reserved_quantity' => 'decimal:3',
        'available_quantity' => 'decimal:3',
        'reorder_point' => 'decimal:3',
        'max_stock' => 'decimal:3',
        'last_movement_at' => 'datetime',
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

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereColumn('available_quantity', '<=', 'reorder_point');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('available_quantity', '<=', 0);
    }

    public function scopeInStock($query)
    {
        return $query->where('available_quantity', '>', 0);
    }

    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Accessors
    public function getStockStatusAttribute(): string
    {
        if ($this->available_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->available_quantity <= $this->reorder_point) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match($this->stock_status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'in_stock' => 'In Stock',
            default => 'Unknown'
        };
    }

    public function getStockStatusColorAttribute(): string
    {
        return match($this->stock_status) {
            'out_of_stock' => 'red',
            'low_stock' => 'yellow',
            'in_stock' => 'green',
            default => 'gray'
        };
    }

    // Methods
    public function updateAvailableQuantity(): void
    {
        $this->available_quantity = $this->quantity - $this->reserved_quantity;
        $this->save();
    }

    public function reserveQuantity(float $quantity): bool
    {
        if ($this->available_quantity >= $quantity) {
            $this->reserved_quantity += $quantity;
            $this->updateAvailableQuantity();
            return true;
        }
        return false;
    }

    public function unreserveQuantity(float $quantity): void
    {
        $this->reserved_quantity = max(0, $this->reserved_quantity - $quantity);
        $this->updateAvailableQuantity();
    }
}