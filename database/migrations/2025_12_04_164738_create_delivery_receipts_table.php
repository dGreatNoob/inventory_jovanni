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
        Schema::create('delivery_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_allocation_id')->constrained()->onDelete('cascade');
            $table->foreignId('box_id')->constrained()->onDelete('cascade');
            $table->string('dr_number')->unique();
            $table->enum('type', ['mother', 'child'])->default('child');
            $table->foreignId('parent_dr_id')->nullable()->constrained('delivery_receipts')->onDelete('set null');
            $table->enum('status', ['pending', 'processing', 'completed'])->default('pending');
            $table->integer('total_items')->default(0);
            $table->integer('scanned_items')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_receipts');
    }
};
