<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\PurchaseOrderStatus;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_num',
        'status',
        'po_type',
        'supplier_id',
        'currency_id',
        'order_date',
        'expected_delivery_date',
        'ordered_by',
        'del_to',
        'del_on',
        'quotation',
        'total_qty',
        'total_price',
        'approver',
        'approved_at',
        'approved_by',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'return_reason',
    ];

    protected $casts = [
        'status' => PurchaseOrderStatus::class,
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'del_on' => 'datetime',
        'loaded_date' => 'date',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'total_qty' => 'decimal:2',
        'total_price' => 'decimal:2',
        
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'del_to');
    }

    public function orderedByUser()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function approverInfo()
    {
        return $this->belongsTo(User::class, 'approver');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function cancelledByUser()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

        public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class, 'purchase_order_id');
    }

    public function productOrders()
    {
        return $this->hasMany(ProductOrder::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function approvalLogs(): HasMany
    {
        return $this->hasMany(PurchaseOrderApprovalLog::class)->orderBy('created_at', 'desc');
    }

    // Status helper methods
    public function getStatusLabelAttribute(): string
    {
        return PurchaseOrderStatus::from($this->status)->label();
    }

    public function isPending(): bool
    {
        return $this->status === PurchaseOrderStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === PurchaseOrderStatus::APPROVED;
    }

    public function isToReceive(): bool
    {
        return $this->status === PurchaseOrderStatus::TO_RECEIVE;
    }

    public function isReceived(): bool
    {
        return $this->status === PurchaseOrderStatus::RECEIVED;
    }

    public function isCancelled(): bool
    {
        return $this->status === PurchaseOrderStatus::CANCELLED;
    }

    /**
     * Conceptual lifecycle helpers
     */
    public function isOpen(): bool
    {
        // Open = not fully received or cancelled
        return ! $this->isClosed();
    }

    public function isClosed(): bool
    {
        // Treat received and cancelled as terminal/closed states
        return $this->isReceived() || $this->isCancelled();
    }

    public function isFulfilled(): bool
    {
        // Fulfilled when all items are received
        return $this->isReceived();
    }

    public function isAllocatable(): bool
    {
        // Eligible for allocation while PO is open and not cancelled
        return $this->isOpen() && ! $this->isCancelled();
    }

    public function canEdit(): bool
    {
        // Allow editing while PO is open and not fully received/cancelled
        return $this->isOpen();
    }

    public function canReceive(): bool
    {
        // Can receive while not cancelled or fully received
        return ! $this->isCancelled() && ! $this->isFulfilled();
    }

    public function canClose(): bool
    {
        return ! $this->isClosed();
    }

    /**
     * Can the user manually close this PO for fulfillment (e.g. short shipment)?
     * True when TO_RECEIVE or APPROVED and not cancelled.
     */
    public function canCloseForFulfillment(): bool
    {
        return ! $this->isCancelled() && ($this->isToReceive() || $this->isApproved());
    }

    /**
     * Can this PO be reopened (RECEIVED → TO_RECEIVE) to receive more or edit lines?
     */
    public function canReopen(): bool
    {
        return $this->isReceived();
    }

    /**
     * Was this PO previously closed for fulfillment and then reopened?
     * Used to enforce same-supplier restriction when adding items.
     */
    public function wasReopened(): bool
    {
        return $this->approvalLogs()->where('action', 'reopened')->exists();
    }

    // Delivery calculations
    public function getTotalDeliveredAttribute()
    {
        return $this->productOrders->sum('received_quantity');
    }

    public function getTotalExpectedAttribute()
    {
        return $this->productOrders->sum(function($order) {
            return $order->expected_qty ?? $order->quantity;
        });
    }

    public function getTotalDestroyedAttribute()
    {
        return $this->productOrders->sum('destroyed_qty') ?? 0;
    }

    public function getTotalReceivedAttribute()
    {
        return $this->total_delivered - $this->total_destroyed;
    }

    public function getDeliveryProgressAttribute()
    {
        $expected = $this->total_expected;
        return $expected > 0 ? ($this->total_delivered / $expected * 100) : 0;
    }

    public function getQualityRateAttribute()
    {
        $delivered = $this->total_delivered;
        return $delivered > 0 ? ($this->total_received / $delivered * 100) : 100;
    }

    public function getFulfillmentRateAttribute()
    {
        $ordered = $this->total_qty;
        return $ordered > 0 ? ($this->total_expected / $ordered * 100) : 100;
    }

    public function hasVarianceAttribute()
    {
        return $this->total_expected > 0 && $this->total_expected != $this->total_qty;
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(PurchaseOrderDelivery::class, 'purchase_order_id');
    }

        /**
     * Get the display status for the PO
     * Shows "Partially Received" if some items received but not complete
     */
    public function getDisplayStatusAttribute()
    {
        // If status is "to_receive", check if any items were received
        if ($this->status->value === 'to_receive') {
            $totalOrdered = $this->productOrders->sum('quantity');
            $totalReceived = $this->productOrders->sum('received_quantity');
            
            if ($totalReceived > 0 && $totalReceived < $totalOrdered) {
                return 'Partially Received';
            }
        }
        
        return $this->status->label();
    }

    /**
     * Get the color for display status
     */
    public function getDisplayStatusColorAttribute()
    {
        // If status is "to_receive", check if any items were received
        if ($this->status->value === 'to_receive') {
            $totalOrdered = $this->productOrders->sum('quantity');
            $totalReceived = $this->productOrders->sum('received_quantity');
            
            if ($totalReceived > 0 && $totalReceived < $totalOrdered) {
                return 'orange'; // Partially received color
            }
        }
        
        return $this->status->color();
    }
}