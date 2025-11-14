<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_receipts')) {
            return;
        }

        Schema::create('sales_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_allocation_id')->constrained('batch_allocations')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->date('date_received')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_receipts');
    }
};