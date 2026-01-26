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
        if (Schema::hasTable('sales_orders')) {
            return;
        }

        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();         
            $table->string('sales_order_number', 50)->unique();  
            $table->string('contact_person_name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('customer_reference', 50)->nullable(); 
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete(); 
            
            // $table->string('product_code', 50)->nullable();
            // $table->text('description')->nullable();          
            // $table->foreignId('product_id')->nullable()->constrained('supply_profiles')->nullOnDelete();
            // $table->unsignedInteger('quantity')->nullable();
            // $table->decimal('unit_price', 15, 2)->nullable();

            $table->decimal('discounts', 8, 2)->nullable()->default(0);
            $table->string('payment_method', 50)->nullable();
            $table->string('shipping_method', 50)->nullable();
            $table->string('payment_terms', 100)->nullable();
            $table->date('delivery_date')->nullable();
            $table->enum('status', ['pending','approved','rejected', 'confirmed','processing','shipped', 'delivered','sold', 'cancelled','returned', 'on hold'])->default('pending');
            $table->unsignedInteger('approver')->nullable();            
            $table->timestamps();
        });         
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
