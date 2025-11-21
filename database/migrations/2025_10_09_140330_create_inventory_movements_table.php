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
        if (Schema::hasTable('inventory_movements')) {
            return;
        }

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('location_id');
            $table->enum('movement_type', [
                'purchase',      // Stock in from supplier
                'sale',          // Stock out from sale
                'return',        // Stock in from return
                'transfer_in',   // Stock in from transfer
                'transfer_out',  // Stock out from transfer
                'adjustment',    // Manual adjustment
                'damage',        // Stock out due to damage
                'theft',         // Stock out due to theft
                'expired'        // Stock out due to expiration
            ]);
            $table->decimal('quantity', 15, 3); // Positive for stock in, negative for stock out
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->enum('reference_type', [
                'purchase_order',
                'sales_order',
                'transfer_order',
                'adjustment',
                'manual'
            ])->nullable();
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of the related document
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional data (batch numbers, expiry dates, etc.)
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index(['product_id', 'location_id', 'created_at']);
            $table->index(['movement_type', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('inventory_locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};