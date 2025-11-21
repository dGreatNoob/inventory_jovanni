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
        if (Schema::hasTable('shipments')) {
            return;
        }

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipping_plan_num')->unique();
            $table->foreignId('sales_order_id')->constrained()->onDelete('cascade');
            $table->string('customer_name')->nullable();
            $table->text('customer_address')->nullable();               
            $table->string('customer_email')->nullable(); 
            $table->string('customer_phone')->nullable();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            
            $table->text('review_remarks')->nullable();
            $table->string('delivery_method')->nullable(); // e.g., courier, pickup
            $table->string('carrier_name')->nullable();
            $table->string('vehicle_plate_number')->nullable();   
            
            $table->text('cancelled_reason')->nullable(); // 
            $table->timestamp('cancelled_at')->nullable();

            $table->json('status_history')->nullable();
            /**
             * [
                    { "status": "pending", "changed_at": "2025-07-30 10:00", "by": "user_1" },
                    { "status": "approved", "changed_at": "2025-07-30 11:15", "by": "user_3" },
                    { "status": "processing", "changed_at": "2025-07-30 14:00", "by": "system" }
                ]
             */

            $table->enum('shipping_priority', [
                'same-day',
                'next-day',
                'normal',
                'scheduled',
                'backorder',
                'rush',
                'express',
            ])->default('normal');           

            $table->text('special_handling_notes')->nullable();
            $table->enum('shipping_status', [
                'approved',
                'failed',
                'returned',
                'processing',
                'pending',
                'ready', 
                'shipped', 
                'in_transit',
                'delivered', 
                'incomplete',
                'damaged',
                'cancelled'
            ])->default('pending');

            $table->date('scheduled_ship_date')->nullable();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();  
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps(); // includes created_at and updated_at
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
