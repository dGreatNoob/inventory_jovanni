<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('purchase_order_deliveries')) {
            return;
        }

        Schema::create('purchase_order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->string('dr_number')->unique();
            $table->date('delivery_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('purchase_order_id');
            $table->index('dr_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_deliveries');
    }
};