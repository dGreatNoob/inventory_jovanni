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
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();  
            $table->integer('quantity')->nullable(); 
            $table->decimal('unit_price', 10, 2)->nullable(); 
            $table->decimal('total_price', 10, 2)->nullable(); 
            $table->string('condition')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('sales_return_items', function (Blueprint $table) {           
             $table->foreign('product_id')
                ->references('id')    // referenced column
                ->on('supply_profiles')      // referenced table
                ->onDelete('cascade'); // cascade delete
     
        }); 


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
    }
};
