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
        'address',
        'contact_num',
        'tin_num'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('supplier')
            ->setDescriptionForEvent(function(string $eventName) {
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
                    return $changes->map(function($new, $field) use ($original) {
                        $old = $original[$field] ?? 'N/A';
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
