<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_receipt_items')) {
            return;
        }

        Schema::create('sales_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_receipt_id')->constrained('sales_receipts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('allocated_qty');
            $table->integer('received_qty')->default(0);
            $table->integer('damaged_qty')->default(0);
            $table->integer('missing_qty')->default(0);
            $table->integer('sold_qty')->default(0);
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->string('sold_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_receipt_items');
    }
};