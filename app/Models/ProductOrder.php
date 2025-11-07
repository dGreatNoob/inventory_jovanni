<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'status',
        'received_quantity',
        'notes',
        // ✅ Batch tracking fields
        'expected_qty',
        'batch_number',
        'receiving_status',
        'receiving_remarks',
        'destroyed_qty',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'received_quantity' => 'integer',
        'expected_qty' => 'integer',
        'destroyed_qty' => 'integer',
    ];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper methods
    public function isFullyReceived(): bool
    {
        $expected = $this->expected_qty ?? $this->quantity;
        $totalDelivered = $this->received_quantity + ($this->destroyed_qty ?? 0);
        
        // ✅ Fully received if total delivered (good + damaged) >= expected
        return $totalDelivered >= $expected;
    }

    public function getRemainingQuantityAttribute()
    {
        return max(0, $this->quantity - $this->received_quantity);
    }
}