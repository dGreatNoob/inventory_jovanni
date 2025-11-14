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
        if (Schema::hasTable('product_colors')) {
            return;
        }

        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique(); // supports zero-padded numeric and future alphanumeric codes
            $table->string('name');
            $table->string('shortcut', 32)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_colors');
    }
};

