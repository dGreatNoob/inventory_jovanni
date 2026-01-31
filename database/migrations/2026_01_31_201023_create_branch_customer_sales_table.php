<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores customer sales from branch inventory with selling_area for reporting.
     */
    public function up(): void
    {
        Schema::create('branch_customer_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('selling_area')->nullable()->comment('Where in the branch the item was sold');
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('branch_customer_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_customer_sale_id')->constrained('branch_customer_sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('product_name')->nullable();
            $table->string('barcode')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_customer_sale_items');
        Schema::dropIfExists('branch_customer_sales');
    }
};
