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
            ->setDescriptionForEvent(function (string $eventName) {
                $name = $this->name ?? 'N/A';
                $address = $this->address ?? 'N/A';
                $contact = $this->contact_num ?? 'N/A';
                $tin = $this->tin_num ?? 'N/A';

                if ($eventName === 'updated') {
                    $changes = collect($this->getChanges())->only($this->fillable);
                    $original = collect($this->getOriginal())->only($changes->keys());

                    if ($changes->isEmpty()) {
                        return '';
                    }

                    return $changes->map(function ($new, $field) use ($original) {
                        $old = $original[$field] ?? 'N/A';

                        // ✅ Fix: Convert arrays (like categories) into readable text
                        if (is_array($old)) {
                            $old = implode(', ', $old);
                        }
                        if (is_array($new)) {
                            $new = implode(', ', $new);
                        }

                        return ucfirst(str_replace('_', ' ', $field)) . ": {$old} → {$new}";
                    })->implode('<br>');
                }

                return "Name: {$name}<br>Address: {$address}<br>Contact: {$contact}<br>TIN: {$tin}";
            });
    }
}