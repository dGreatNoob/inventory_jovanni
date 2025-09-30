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
        Schema::table('supply_profiles', function (Blueprint $table) {
            $table->integer('low_stock_threshold_percentage')->default(20)->after('supply_min_qty')
                ->comment('Percentage of minimum quantity to trigger low stock warning (e.g., 20 = 20% of min_qty)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_profiles', function (Blueprint $table) {
            $table->dropColumn('low_stock_threshold_percentage');
        });
    }
};
