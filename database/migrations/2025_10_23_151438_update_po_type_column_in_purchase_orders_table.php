<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Change po_type to allow longer values
            $table->string('po_type', 50)->change(); // or use enum
            
            // OR use enum if you have specific values:
            // $table->enum('po_type', ['raw_mats', 'products', 'supply', 'equipment'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('po_type', 10)->change(); // revert to original size
        });
    }
};