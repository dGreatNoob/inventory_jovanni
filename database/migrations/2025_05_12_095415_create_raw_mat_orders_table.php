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
        if (Schema::hasTable('raw_mat_orders')) {
            return;
        }

        Schema::create('raw_mat_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\PurchaseOrder::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\RawMatProfile::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_mat_orders');
    }
};
