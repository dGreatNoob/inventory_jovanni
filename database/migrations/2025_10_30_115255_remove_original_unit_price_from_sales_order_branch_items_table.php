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
        Schema::table('sales_order_branch_items', function (Blueprint $table) {
            $table->dropColumn('original_unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_order_branch_items', function (Blueprint $table) {
            $table->decimal('original_unit_price', 10, 2)->after('unit_price');
        });
    }
};
