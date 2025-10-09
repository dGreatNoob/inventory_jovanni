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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id')->default(1);
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->string('name');
            $table->json('specs')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->text('remarks')->nullable();
            $table->string('uom')->default('pcs');
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_code')->nullable();
            $table->decimal('price', 15, 2);
            $table->text('price_note')->nullable();
            $table->decimal('cost', 15, 2);
            $table->integer('shelf_life_days')->nullable();
            $table->string('pict_name')->nullable();
            $table->boolean('disabled')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['entity_id', 'disabled']);
            $table->index(['category_id']);
            $table->index(['supplier_id']);
            $table->index(['sku']);
            $table->index(['barcode']);
            
            // Foreign keys
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};