<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SupplyProfile extends Model
{
    /** @use HasFactory<\Database\Factories\SupplyProfileFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
         'supply_sku',
        'supply_item_class',
        'item_type_id',
        'allocation_id',
        'supply_description',
        'supply_qty',
        'supply_uom',
        'low_stock_threshold_percentage',
        'supply_price1',
        'supply_price2',
        'supply_price3',
        'unit_cost',    
    ];

    public function itemType()
    {
        return $this->belongsTo(ItemType::class);
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    public function supplyOrders(): HasMany
    {
        return $this->hasMany(SupplyOrder::class);
    }

    // Optional reverse relation
    public function salesOrderItems()
    {
        return $this->hasMany(SalesOrderItem::class, 'product_id');
    }

    public function supplyBatches(): HasMany
    {
        return $this->hasMany(SupplyBatch::class);
    }

    public function activeBatches(): HasMany
    {
        return $this->hasMany(SupplyBatch::class)->where('status', 'active')->where('current_qty', '>', 0);
    }

    public function expiringSoonBatches(int $days = 30): HasMany
    {
        return $this->hasMany(SupplyBatch::class)->expiringSoon($days);
    }

    // Helper methods for batch management
    public function isConsumable(): bool
    {
        return $this->supply_item_class === 'consumable';
    }

    public function getTotalBatchQuantity(): float
    {
        return $this->activeBatches()->sum('current_qty');
    }

    public function hasExpiredBatches(): bool
    {
        return $this->supplyBatches()->expired()->exists();
    }

    public function isLowStock(): bool
    {
        // Use a fixed threshold of 10 units or 5% of current stock, whichever is higher
        $thresholdQty = max(10, $this->supply_qty * 0.05);
        return $this->supply_qty <= $thresholdQty;
    }

    public function getLowStockThresholdQuantity(): float
    {
        // Use a fixed threshold of 10 units or 5% of current stock, whichever is higher
        return max(10, $this->supply_qty * 0.05);
    }

    public static function getUnitOfMeasureOptions(): array
    {
        return [
            // Weight
            'g' => 'Grams (g)',
            'kg' => 'Kilograms (kg)',
            'lb' => 'Pounds (lb)',
            'oz' => 'Ounces (oz)',
            
            // Volume
            'ml' => 'Milliliters (ml)',
            'l' => 'Liters (l)',
            'gal' => 'Gallons (gal)',
            'fl oz' => 'Fluid Ounces (fl oz)',
            
            // Length
            'mm' => 'Millimeters (mm)',
            'cm' => 'Centimeters (cm)',
            'm' => 'Meters (m)',
            'in' => 'Inches (in)',
            'ft' => 'Feet (ft)',
            
            // Area
            'sq mm' => 'Square Millimeters (sq mm)',
            'sq cm' => 'Square Centimeters (sq cm)',
            'sq m' => 'Square Meters (sq m)',
            'sq ft' => 'Square Feet (sq ft)',
            
            // Count
            'pcs' => 'Pieces (pcs)',
            'box' => 'Boxes (box)',
            'pack' => 'Packs (pack)',
            'set' => 'Sets (set)',
            'pair' => 'Pairs (pair)',
            'dozen' => 'Dozen (dozen)',
            'unit' => 'Units (unit)',
            
            // Printing specific
            'sheets' => 'Sheets',
            'rolls' => 'Rolls',
            'reams' => 'Reams',
            'gsm' => 'GSM (grams per square meter)',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('supply_profile')
            ->setDescriptionForEvent(function(string $eventName) {
                $itemTypeName = $this->relationLoaded('itemType') ? ($this->itemType->name ?? 'N/A') : (\App\Models\ItemType::find($this->item_type_id)->name ?? 'N/A');
                $allocationName = $this->relationLoaded('allocation') ? ($this->allocation->name ?? 'N/A') : (\App\Models\Allocation::find($this->allocation_id)->name ?? 'N/A');
                if ($eventName === 'updated') {
                    $changes = collect($this->getChanges())->only($this->fillable);
                    $original = collect($this->getOriginal())->only($changes->keys());
                    if ($changes->isEmpty()) {
                        return '';
                    }
                    // Custom logic for supply_qty
                    if ($changes->has('supply_qty')) {
                        $oldQty = $original['supply_qty'] ?? 0;
                        $newQty = $changes['supply_qty'];
                        $desc = ($this->supply_description ?? 'Supply') . ': ' . $oldQty . ' → ' . $newQty;
                        // Set the log name/action
                        if ($newQty > $oldQty) {
                            activity()->useLog('Stock-in');
                        } elseif ($newQty < $oldQty) {
                            activity()->useLog('Stock-out');
                        }
                        return $desc;
                    }
                    return $changes->map(function($new, $field) use ($original) {
                        $old = $original[$field] ?? 'N/A';
                        if ($field === 'item_type_id') {
                            $oldName = \App\Models\ItemType::find($old)->name ?? $old;
                            $newName = \App\Models\ItemType::find($new)->name ?? $new;
                            return 'Item Type: ' . $oldName . ' → ' . $newName;
                        }
                        if ($field === 'allocation_id') {
                            $oldName = \App\Models\Allocation::find($old)->name ?? $old;
                            $newName = \App\Models\Allocation::find($new)->name ?? $new;
                            return 'Allocation: ' . $oldName . ' → ' . $newName;
                        }
                        return ucfirst(str_replace('_', ' ', $field)) . ": {$old} → {$new}";
                    })->implode('<br>');
                }
                // For create/delete, log all details
                $fields = [
                    'SKU' => $this->supply_sku ?? 'N/A',
                    'Item Class' => $this->supply_item_class ?? 'N/A',
                    'Item Type' => $itemTypeName,
                    'Allocation' => $allocationName,
                    'Description' => $this->supply_description ?? 'N/A',
                    'Qty' => $this->supply_qty ?? 'N/A',
                    'UOM' => $this->supply_uom ?? 'N/A',
                    'Min Qty' => $this->supply_min_qty ?? 'N/A',
                    'Price 1' => $this->supply_price1 ?? 'N/A',
                    'Price 2' => $this->supply_price2 ?? 'N/A',
                    'Price 3' => $this->supply_price3 ?? 'N/A',
                ];
                return collect($fields)->map(function($v, $k) { return "$k: $v"; })->implode('<br>');
            });
    }

    public function tapActivity(\Spatie\Activitylog\Models\Activity $activity, string $eventName)
    {
        if ($eventName === 'updated' && $activity->properties->has('attributes.supply_qty') && $activity->properties->has('old.supply_qty')) {
            $oldQty = $activity->properties['old']['supply_qty'];
            $newQty = $activity->properties['attributes']['supply_qty'];
            // Set event to 'updated' for supply_qty changes
            $activity->event = 'updated';
        }
    }
}
