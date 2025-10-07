<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'filename',
        'alt_text',
        'original_filename',
        'mime_type',
        'file_size',
        'width',
        'height',
        'is_primary',
        'sort_order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    // Accessors
    public function getUrlAttribute(): string
    {
        return asset('storage/product-images/' . $this->filename);
    }

    public function getThumbnailUrlAttribute(): string
    {
        $pathInfo = pathinfo($this->filename);
        $thumbnailName = $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        return asset('storage/product-images/thumbnails/' . $thumbnailName);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}