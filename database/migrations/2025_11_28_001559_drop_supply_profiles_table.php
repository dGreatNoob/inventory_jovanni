<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drops supply_profiles table and related foreign keys.
     * Supply profiles are no longer necessary - using Product model instead.
     */
    public function up(): void
    {
        // Drop foreign keys first, then drop the table
        // Using dropIfExists for idempotency - safe to run multiple times
        
        // Drop foreign key from purchase_order_items
        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
            });
        }

        // Drop foreign key from supply_orders
        if (Schema::hasTable('supply_orders')) {
            Schema::table('supply_orders', function (Blueprint $table) {
                $table->dropForeign(['supply_profile_id']);
            });
        }

        // Drop foreign key from supply_batches
        if (Schema::hasTable('supply_batches')) {
            Schema::table('supply_batches', function (Blueprint $table) {
                $table->dropForeign(['supply_profile_id']);
            });
        }

        // Drop the supply_profiles table
        Schema::dropIfExists('supply_profiles');
    }

    /**
     * Reverse the migrations.
     * 
     * Recreates the supply_profiles table with its original structure.
     * Note: This will recreate an empty table - data will be lost.
     */
    public function down(): void
    {
        if (!Schema::hasTable('supply_profiles')) {
            Schema::create('supply_profiles', function (Blueprint $table) {
                $table->id();
                $table->string('supply_sku')->unique();
                $table->string('supply_item_class')->nullable();
                $table->foreignId('item_type_id')->nullable()->constrained('item_types')->onDelete('set null');
                $table->text('supply_description')->nullable();
                $table->decimal('supply_qty', 10, 2)->default(0);
                $table->string('supply_uom')->nullable();
                $table->decimal('supply_min_qty', 10, 2)->nullable();
                $table->integer('low_stock_threshold_percentage')->default(20);
                $table->decimal('supply_price1', 10, 2)->nullable();
                $table->decimal('supply_price2', 10, 2)->nullable();
                $table->decimal('supply_price3', 10, 2)->nullable();
                $table->decimal('unit_cost', 10, 2)->nullable();
                $table->string('supply_status')->default('active');
                $table->timestamps();
            });
        }

        // Recreate foreign keys
        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                $table->foreign('product_id')->references('id')->on('supply_profiles')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('supply_orders')) {
            Schema::table('supply_orders', function (Blueprint $table) {
                $table->foreign('supply_profile_id')->references('id')->on('supply_profiles')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('supply_batches')) {
            Schema::table('supply_batches', function (Blueprint $table) {
                $table->foreign('supply_profile_id')->references('id')->on('supply_profiles')->onDelete('cascade');
            });
        }
    }
};
