<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'entity_id',
        'name',
        'code',
        'type',
        'address',
        'manager_name',
        'contact_phone',
        'capacity_sqft',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity_sqft' => 'decimal:2',
    ];

    // Relationships
    public function productInventory(): HasMany
    {
        return $this->hasMany(ProductInventory::class, 'location_id');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(ProductInventory::class, 'location_id');
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'location_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByEntity($query, $entityId)
    {
        return $query->where('entity_id', $entityId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return $this->name . ' (' . $this->code . ')';
    }
}