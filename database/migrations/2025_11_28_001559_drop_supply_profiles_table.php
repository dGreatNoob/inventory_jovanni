<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        
        // Drop foreign key from purchase_order_items (if it exists)
        if (Schema::hasTable('purchase_order_items')) {
            $this->dropForeignKeyIfExists('purchase_order_items', 'product_id', 'supply_profiles');
        }

        // Drop foreign key from sales_order_items (if it exists)
        if (Schema::hasTable('sales_order_items')) {
            $this->dropForeignKeyIfExists('sales_order_items', 'product_id', 'supply_profiles');
        }

        // Drop foreign key from supply_orders (if it exists)
        if (Schema::hasTable('supply_orders')) {
            $this->dropForeignKeyIfExists('supply_orders', 'supply_profile_id', 'supply_profiles');
        }

        // Drop foreign key from supply_batches (if it exists)
        if (Schema::hasTable('supply_batches')) {
            $this->dropForeignKeyIfExists('supply_batches', 'supply_profile_id', 'supply_profiles');
        }

        // Drop the supply_profiles table
        Schema::dropIfExists('supply_profiles');
    }

    /**
     * Drop a foreign key if it exists.
     * 
     * @param string $tableName
     * @param string $columnName
     * @param string $referencedTable
     */
    private function dropForeignKeyIfExists(string $tableName, string $columnName, string $referencedTable): void
    {
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND COLUMN_NAME = ?
            AND REFERENCED_TABLE_NAME = ?
        ", [$tableName, $columnName, $referencedTable]);

        foreach ($foreignKeys as $fk) {
            Schema::table($tableName, function (Blueprint $table) use ($fk) {
                $table->dropForeign($fk->CONSTRAINT_NAME);
            });
        }
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
