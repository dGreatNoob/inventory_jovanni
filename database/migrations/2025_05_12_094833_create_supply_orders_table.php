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
        Schema::create('supply_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\PurchaseOrder::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\SupplyProfile::class)->constrained()->onDelete('cascade');
            $table->decimal('unit_price');
            $table->decimal('order_total_price');
            $table->decimal('final_total_price')->nullable();
            $table->decimal('order_qty', 10, 2);
            $table->decimal('received_qty', 10, 2)->nullable();
            $table->enum('remarks',[
                'pending',
                'accepted',
                'rejected',
            ])->default('pending');
            $table->string('comment')->nullable();
            $table->enum('status',[
                'pending',
                'delivered',
                'cancelled',
                'failed',
            ])->default('pending');
            $table->date('date_delivered')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_orders');
    }
};
