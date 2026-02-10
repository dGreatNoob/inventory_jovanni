<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Expected inventory ledger for Manual Stock-In with PO Reference approach.
     * Tracks expected quantity from POs; reconciled on real stock-in.
     */
    public function up(): void
    {
        Schema::create('product_inventory_expected', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('purchase_order_id');
            $table->decimal('expected_quantity', 15, 3)->default(0);
            $table->decimal('received_quantity', 15, 3)->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'purchase_order_id']);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inventory_expected');
    }
};
