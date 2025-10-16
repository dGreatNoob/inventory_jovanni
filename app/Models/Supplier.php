<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'entity_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'terms',
        'tax_id',
        'credit_limit',
        'payment_terms_days',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2',
    ];

    // Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
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
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->country,
            $this->postal_code
        ]);
        
        return implode(', ', $parts);
    }

    public function getContactInfoAttribute(): string
    {
        $parts = array_filter([
            $this->contact_person,
            $this->phone,
            $this->email
        ]);
        
        return implode(' | ', $parts);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('supplier')
            ->setDescriptionForEvent(function(string $eventName) {
                return "Supplier {$eventName}: {$this->name}";
            });
    }
}