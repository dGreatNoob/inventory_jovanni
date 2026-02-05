<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductOrder;
use App\Enums\PurchaseOrderStatus;

/**
 * Helper for computing "available to allocate" quantities in the allocation workflow.
 * Available = current stock (ProductInventory.available_quantity) + expected receipts from PO (ProductOrder.remaining).
 */
class AllocationAvailabilityHelper
{
    /**
     * Statuses for POs that have not yet been fully received (expected quantity is meaningful).
     */
    protected static function validPOStatuses(): array
    {
        return [
            PurchaseOrderStatus::APPROVED->value,
            PurchaseOrderStatus::TO_RECEIVE->value,
        ];
    }

    /**
     * Get expected (remaining) quantity for a product from a specific PO.
     * Only includes POs with status approved or to_receive.
     * Returns 0 when purchaseOrderId is null.
     */
    public static function getExpectedQuantityFromPO(Product $product, ?int $purchaseOrderId): float
    {
        if ($purchaseOrderId === null) {
            return 0.0;
        }

        $remaining = ProductOrder::where('purchase_order_id', $purchaseOrderId)
            ->where('product_id', $product->id)
            ->whereHas('purchaseOrder', function ($q) {
                $q->whereIn('status', self::validPOStatuses());
            })
            ->get()
            ->sum(fn ($po) => $po->remaining_quantity);

        return (float) $remaining;
    }

    /**
     * Get available quantity to allocate for a product.
     * = stock (ProductInventory.available_quantity) + expected from PO.
     */
    public static function getAvailableToAllocate(Product $product, ?int $purchaseOrderId): float
    {
        $stock = (float) $product->available_quantity;
        $expected = self::getExpectedQuantityFromPO($product, $purchaseOrderId);

        return $stock + $expected;
    }

    /**
     * Get stock (available quantity from ProductInventory) for a product.
     */
    public static function getStockQuantity(Product $product): float
    {
        return (float) $product->available_quantity;
    }
}
