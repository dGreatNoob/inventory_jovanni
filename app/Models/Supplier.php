<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
    'name',
    'code',
    'address',
    'contact_person',
    'contact_num',
    'email',
    'tin_num',
    'status',
    'categories'
    ];

    protected $casts = [
    'categories' => 'array',
    ];

    const CATEGORIES = [
    'Bag',
    'Travel Bag', 
    'Sports Bag',
    'Purse',
    'Accessories',
    'Wallets'
    ];

    // In Livewire component
    public function mount()
    {
        $this->availableCategories = Supplier::CATEGORIES;
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
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
