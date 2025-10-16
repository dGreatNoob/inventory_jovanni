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
        Schema::create('product_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('location_id');
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('reserved_quantity', 15, 3)->default(0);
            $table->decimal('available_quantity', 15, 3)->default(0); // quantity - reserved_quantity
            $table->decimal('reorder_point', 15, 3)->default(0);
            $table->decimal('max_stock', 15, 3)->nullable();
            $table->timestamp('last_movement_at')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent duplicate product-location combinations
            $table->unique(['product_id', 'location_id']);
            $table->index(['location_id', 'available_quantity']);
            $table->index(['product_id', 'available_quantity']);
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('inventory_locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inventory');
    }
};