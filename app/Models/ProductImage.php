<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        if (empty($this->filename)) {
            return asset('images/placeholder.png');
        }
        
        // Use Storage::url() for proper URL generation that works in all environments
        if (Storage::disk('public')->exists('photos/' . $this->filename)) {
            return Storage::disk('public')->url('photos/' . $this->filename);
        }
        
        // Fallback to asset() if Storage URL doesn't work
        return asset('storage/photos/' . $this->filename);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if (empty($this->filename)) {
            return asset('images/placeholder.png');
        }
        
        $pathInfo = pathinfo($this->filename);
        $thumbnailName = $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        
        // Use Storage::url() for proper URL generation
        if (Storage::disk('public')->exists('photos/thumbnails/' . $thumbnailName)) {
            return Storage::disk('public')->url('photos/thumbnails/' . $thumbnailName);
        }
        
        // Fallback to asset() if Storage URL doesn't work
        return asset('storage/photos/thumbnails/' . $thumbnailName);
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