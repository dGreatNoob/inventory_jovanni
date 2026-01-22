<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drops unused tables that were inherited from the old repository fork.
     * These tables have models but are not actively used in the application.
     */
    public function up(): void
    {
        // Drop item_classes table - not used (SupplyProfile uses supply_item_class as string, not relationship)
        if (Schema::hasTable('item_classes')) {
            Schema::dropIfExists('item_classes');
        }

        // Drop stock_batches table - not used (replaced by product_batches and supply_batches)
        if (Schema::hasTable('stock_batches')) {
            Schema::dropIfExists('stock_batches');
        }

        // Note: logs table doesn't exist (already removed or never created)
        // The Log model exists but the table was replaced by activity_log (Spatie Activity Log)
    }

    /**
     * Reverse the migrations.
     * 
     * Recreates the dropped tables with their original structure.
     * Note: This will recreate empty tables - data will be lost.
     */
    public function down(): void
    {
        // Recreate item_classes table
        if (!Schema::hasTable('item_classes')) {
            Schema::create('item_classes', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Recreate stock_batches table
        if (!Schema::hasTable('stock_batches')) {
            Schema::create('stock_batches', function (Blueprint $table) {
                $table->id();
                $table->date('transaction_date')->nullable();
                $table->text('remarks')->nullable();
                $table->string('auto_ref_no')->nullable();
                $table->timestamps();
            });
        }
    }
};
