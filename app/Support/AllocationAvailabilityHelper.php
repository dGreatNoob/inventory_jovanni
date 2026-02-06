<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductInventoryExpected;
use App\Enums\PurchaseOrderStatus;

/**
 * Helper for computing "available to allocate" quantities in the allocation workflow.
 * Available = current stock (ProductInventory.quantity) + expected from ledger (ProductInventoryExpected).
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
     * Reads from ProductInventoryExpected ledger.
     * Formula: SUM(expected_quantity - received_quantity) for product+PO.
     * Only includes POs with status approved or to_receive.
     * Returns 0 when purchaseOrderId is null.
     */
    public static function getExpectedQuantityFromPO(Product $product, ?int $purchaseOrderId): float
    {
        if ($purchaseOrderId === null) {
            return 0.0;
        }

        $netExpected = ProductInventoryExpected::where('product_id', $product->id)
            ->where('purchase_order_id', $purchaseOrderId)
            ->whereHas('purchaseOrder', function ($q) {
                $q->whereIn('status', self::validPOStatuses());
            })
            ->get()
            ->sum(fn ($record) => (float) $record->expected_quantity - (float) $record->received_quantity);

        return (float) max(0, $netExpected);
    }

    /**
     * Get available quantity to allocate for a product.
     * = stock (ProductInventory.quantity) + expected from PO.
     */
    public static function getAvailableToAllocate(Product $product, ?int $purchaseOrderId): float
    {
        $stock = self::getStockQuantity($product);
        $expected = self::getExpectedQuantityFromPO($product, $purchaseOrderId);

        return $stock + $expected;
    }

    /**
     * Get stock quantity for a product.
     * Uses total_quantity (ProductInventory.quantity) to match Product Masterlist Stock column.
     */
    public static function getStockQuantity(Product $product): float
    {
        return (float) $product->total_quantity;
    }
}
