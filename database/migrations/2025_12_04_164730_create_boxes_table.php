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
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_allocation_id')->constrained()->onDelete('cascade');
            $table->string('box_number')->unique();
            $table->enum('status', ['open', 'full', 'closed'])->default('open');
            $table->integer('capacity')->default(50); // Default capacity for products
            $table->integer('current_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
