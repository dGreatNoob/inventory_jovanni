<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use App\Models\ProductInventoryExpected;
use App\Enums\PurchaseOrderStatus;

/**
 * Auto-sync ProductInventoryExpected when PO status is approved or to_receive.
 * Keeps expected ledger in sync with ProductOrder lines.
 */
class PurchaseOrderObserver
{
    protected function validStatuses(): array
    {
        return [
            PurchaseOrderStatus::APPROVED->value,
            PurchaseOrderStatus::TO_RECEIVE->value,
        ];
    }

    public function saved(PurchaseOrder $purchaseOrder): void
    {
        $status = $purchaseOrder->status instanceof PurchaseOrderStatus
            ? $purchaseOrder->status->value
            : (string) $purchaseOrder->status;

        if (!in_array($status, $this->validStatuses())) {
            return;
        }

        // Only sync when status transitions to approved/to_receive (or on create with that status)
        if (!$purchaseOrder->wasRecentlyCreated && !$purchaseOrder->wasChanged('status')) {
            return;
        }

        $purchaseOrder->loadMissing('productOrders');

        foreach ($purchaseOrder->productOrders as $productOrder) {
            ProductInventoryExpected::updateOrCreate(
                [
                    'product_id' => $productOrder->product_id,
                    'purchase_order_id' => $purchaseOrder->id,
                ],
                [
                    'expected_quantity' => (float) ($productOrder->quantity ?? 0),
                    'received_quantity' => (float) ($productOrder->received_quantity ?? 0),
                ]
            );
        }
    }
}
