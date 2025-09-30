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
            $table->decimal('unit_cost', 10, 2)->default(0.00)->after('supply_price3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_profiles', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
        });
    }
};
