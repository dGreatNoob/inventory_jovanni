<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'entity_id',
        'name',
        'description',
        'parent_id',
        'sort_order',
        'slug',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
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

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->parent ? $this->parent->name . ' > ' . $this->name : $this->name;
    }

    public function getIndentedNameAttribute(): string
    {
        return $this->parent ? '↳ ' . $this->name : $this->name;
    }

    public function isRootCategory(): bool
    {
        return is_null($this->parent_id);
    }

    public function isSubCategory(): bool
    {
        return !is_null($this->parent_id);
    }

    // Get hierarchical category list for dropdowns
    public static function getHierarchicalList($entityId = null)
    {
        $query = self::with('parent')
            ->active()
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($entityId) {
            $query->byEntity($entityId);
        }

        return $query->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'full_name' => $category->full_name,
                'indented_name' => $category->indented_name,
                'parent_id' => $category->parent_id,
                'is_root' => $category->isRootCategory(),
            ];
        });
    }
}