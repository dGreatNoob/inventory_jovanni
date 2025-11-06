<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'entity_id',
        'sku',
        'barcode',
        'name',
        'specs',
        'category_id',
        'remarks',
        'uom',
        'supplier_id',
        'supplier_code',
        'price',
        'price_note',
        'cost',
        'shelf_life_days',
        'pict_name',
        'disabled',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'specs' => 'array',
        'disabled' => 'boolean',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(ProductInventory::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // Scopes
    public function scopeActive($query): Builder
    {
        return $query->where('disabled', false);
    }

    public function scopeByEntity($query, $entityId): Builder
    {
        return $query->where('entity_id', $entityId);
    }

    public function scopeByCategory($query, $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySupplier($query, $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeSearch($query, $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%")
              ->orWhere('remarks', 'like', "%{$search}%")
              ->orWhere('supplier_code', 'like', "%{$search}%")
              ->orWhereJsonContains('specs', $search)
              ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                  $supplierQuery->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('category', function ($categoryQuery) use ($search) {
                  $categoryQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    // Accessors
    public function getPrimaryImageAttribute(): ?string
    {
        $primaryImage = $this->images()->where('is_primary', true)->first();
        if ($primaryImage) {
            return $primaryImage->filename;
        }
        
        // If no primary image, return the first image
        $firstImage = $this->images()->first();
        if ($firstImage) {
            return $firstImage->filename;
        }
        
        // Fallback to pict_name if it exists
        return $this->pict_name;
    }

    public function getTotalQuantityAttribute(): float
    {
        return $this->inventory()->sum('quantity');
    }

    public function getAvailableQuantityAttribute(): float
    {
        return $this->inventory()->sum('available_quantity');
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->cost == 0) return 0;
        return (($this->price - $this->cost) / $this->cost) * 100;
    }

    public function getStatusAttribute(): string
    {
        return $this->disabled ? 'inactive' : 'active';
    }
}