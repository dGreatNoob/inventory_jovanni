<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Expected inventory ledger for Manual Stock-In with PO Reference approach.
 *
 * Tracks expected quantity from POs; reconciled on real stock-in.
 * Available to allocate = ProductInventory.available_quantity + SUM(expected_quantity - received_quantity).
 */
class ProductInventoryExpected extends Model
{
    use HasFactory;

    protected $table = 'product_inventory_expected';

    protected $fillable = [
        'product_id',
        'purchase_order_id',
        'expected_quantity',
        'received_quantity',
    ];

    protected $casts = [
        'expected_quantity' => 'decimal:3',
        'received_quantity' => 'decimal:3',
    ];

    /**
     * Product this expected record belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Purchase order this expected record is from.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Scope by product.
     */
    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope by purchase order.
     */
    public function scopeByPurchaseOrder($query, int $purchaseOrderId)
    {
        return $query->where('purchase_order_id', $purchaseOrderId);
    }

    /**
     * Net expected (expected - received) for this record.
     */
    public function getNetExpectedAttribute(): float
    {
        return max(0, (float) $this->expected_quantity - (float) $this->received_quantity);
    }
}
