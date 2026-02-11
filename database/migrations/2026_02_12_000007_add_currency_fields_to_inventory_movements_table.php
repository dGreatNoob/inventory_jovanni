<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Phase 3: Stock-in & cost recording — store currency, rate used, and original unit cost for audit.
     */
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('currency_id')->nullable()->after('total_cost');
            $table->decimal('exchange_rate_applied', 15, 6)->nullable()->after('currency_id');
            $table->decimal('unit_cost_original', 15, 2)->nullable()->after('exchange_rate_applied');

            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn(['currency_id', 'exchange_rate_applied', 'unit_cost_original']);
        });
    }
};
