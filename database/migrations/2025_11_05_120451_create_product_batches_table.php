<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_batches')) {
            return;
        }

        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('set null'); // ✅ ADD THIS LINE
            $table->string('batch_number');
            $table->decimal('initial_qty', 10, 2)->default(0);
            $table->decimal('current_qty', 10, 2)->default(0);
            $table->date('received_date');
            $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['product_id', 'batch_number']);
            $table->index('purchase_order_id'); // ✅ ADD THIS LINE
        });

        Log::info('product_batches table created successfully', [
            'user' => 'Wts135',
            'timestamp' => '2025-11-11 07:53:16',
        ]);
    }

    public function down(): void
    {
        Log::info('Dropping product_batches table', [
            'user' => 'Wts135',
            'timestamp' => '2025-11-11 07:53:16',
        ]);

        Schema::dropIfExists('product_batches');
    }
};