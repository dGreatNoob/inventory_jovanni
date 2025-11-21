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
        if (Schema::hasTable('raw_mat_profiles')) {
            return;
        }

        Schema::create('raw_mat_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('gsm');
            $table->integer('width_size');
            $table->string('classification');
            $table->string('supplier');
            $table->string('country_origin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_mat_profiles');
    }
};
