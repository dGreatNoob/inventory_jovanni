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
        Schema::create('branch_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_transfer_id')->constrained('branch_transfers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('branch_allocation_item_id')->constrained('branch_allocation_items');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_value', 10, 2)->nullable();
            $table->enum('status', ['pending', 'transferred', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_transfer_id', 'status']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_transfer_items');
    }
};
