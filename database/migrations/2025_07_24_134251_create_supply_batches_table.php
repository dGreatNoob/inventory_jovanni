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
        if (Schema::hasTable('supply_batches')) {
            return;
        }

        Schema::create('supply_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\SupplyProfile::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\SupplyOrder::class)->nullable()->constrained()->onDelete('set null');
            $table->string('batch_number', 100); // Unique batch identifier
            $table->date('expiration_date')->nullable(); // For consumables with expiry
            $table->date('manufactured_date')->nullable(); // Manufacturing/production date
            $table->decimal('initial_qty', 10, 2); // Initial quantity received for this batch
            $table->decimal('current_qty', 10, 2); // Current remaining quantity
            $table->string('location', 100)->nullable(); // Storage location
            $table->text('notes')->nullable(); // Any special notes about the batch
            $table->enum('status', ['active', 'expired', 'depleted', 'quarantined'])->default('active');
            $table->date('received_date'); // Date when this batch was received
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null'); // Who received it
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['supply_profile_id', 'status']);
            $table->index(['expiration_date', 'status']);
            $table->index('batch_number');
            
            // Unique constraint to prevent duplicate batch numbers for the same product
            $table->unique(['supply_profile_id', 'batch_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_batches');
    }
};
