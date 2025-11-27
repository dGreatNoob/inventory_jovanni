<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplyOrder extends Model
{
    /** @use HasFactory<\Database\Factories\SupplyOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'supply_profile_id',
        'unit_price',
        'order_total_price',
        'final_total_price',
        'order_qty',
        'received_qty',
        'receiving_status',
        'receiving_remarks',
        'remark',
        'comment',
        'status',
        'price_tier'
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // supplyProfile() relationship removed - SupplyProfile module has been removed
    // Use Product model instead if needed
}
