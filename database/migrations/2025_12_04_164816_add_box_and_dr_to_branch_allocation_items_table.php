<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('branch_allocation_items', function (Blueprint $table) {
            $table->foreignId('box_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('delivery_receipt_id')->nullable()->constrained()->onDelete('set null');

            // Add product snapshot fields for historical data integrity
            $table->string('product_snapshot_name')->nullable();
            $table->string('product_snapshot_sku')->nullable();
            $table->string('product_snapshot_barcode')->nullable();
            $table->json('product_snapshot_specs')->nullable();
            $table->decimal('product_snapshot_price', 10, 2)->nullable();
            $table->string('product_snapshot_uom')->nullable();
            $table->timestamp('product_snapshot_created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_allocation_items', function (Blueprint $table) {
            $table->dropForeign(['box_id']);
            $table->dropColumn('box_id');
            $table->dropForeign(['delivery_receipt_id']);
            $table->dropColumn('delivery_receipt_id');

            // Remove product snapshot fields
            $table->dropColumn([
                'product_snapshot_name',
                'product_snapshot_sku',
                'product_snapshot_barcode',
                'product_snapshot_specs',
                'product_snapshot_price',
                'product_snapshot_uom',
                'product_snapshot_created_at'
            ]);
        });
    }
};
