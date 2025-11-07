<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderDelivery extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'dr_number',
        'delivery_date',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function supplyBatches(): HasMany
    {
        return $this->hasMany(SupplyBatch::class, 'purchase_order_id', 'purchase_order_id');
    }

    /**
     * Generate a unique DR number
     */
    public static function generateNewDONumber(): string
    {
        $year = date('Y');
        $prefix = "DR-{$year}-";
        
        $lastDR = self::where('dr_number', 'like', "{$prefix}%")
            ->orderBy('dr_number', 'desc')
            ->first();
        
        if ($lastDR) {
            $lastNumber = (int) str_replace($prefix, '', $lastDR->dr_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}