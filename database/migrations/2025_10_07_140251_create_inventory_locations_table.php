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
        Schema::create('inventory_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id')->default(1); // Multi-tenant support
            $table->string('name');
            $table->string('code')->unique(); // Location code (e.g., WH001, ST001)
            $table->enum('type', ['warehouse', 'store', 'display', 'transit'])->default('warehouse');
            $table->text('address')->nullable();
            $table->string('manager_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->decimal('capacity_sqft', 10, 2)->nullable(); // Storage capacity
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['entity_id', 'type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_locations');
    }
};