<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * One-time seed: Backfill ProductInventoryExpected from ProductOrder for existing approved/to_receive POs.
     * Safe to re-run (uses INSERT IGNORE / firstOrCreate logic).
     */
    public function up(): void
    {
        $validStatuses = ['approved', 'to_receive'];

        $productOrders = DB::table('product_orders')
            ->join('purchase_orders', 'product_orders.purchase_order_id', '=', 'purchase_orders.id')
            ->whereIn('purchase_orders.status', $validStatuses)
            ->select(
                'product_orders.product_id',
                'product_orders.purchase_order_id',
                'product_orders.quantity',
                'product_orders.received_quantity'
            )
            ->get();

        foreach ($productOrders as $po) {
            $exists = DB::table('product_inventory_expected')
                ->where('product_id', $po->product_id)
                ->where('purchase_order_id', $po->purchase_order_id)
                ->exists();

            if (!$exists) {
                DB::table('product_inventory_expected')->insert([
                    'product_id' => $po->product_id,
                    'purchase_order_id' => $po->purchase_order_id,
                    'expected_quantity' => (float) ($po->quantity ?? 0),
                    'received_quantity' => (float) ($po->received_quantity ?? 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     * Does not remove records - seeding is additive.
     */
    public function down(): void
    {
        // No-op: we don't remove seeded data on rollback
        // to avoid losing manual entries made after seeding
    }
};
