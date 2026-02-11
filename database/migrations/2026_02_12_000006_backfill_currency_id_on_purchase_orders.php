<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill currency_id for existing purchase_orders and product_orders (PHP base).
     */
    public function up(): void
    {
        $phpId = DB::table('currencies')->where('code', 'PHP')->value('id');
        if (!$phpId) {
            return;
        }

        DB::table('purchase_orders')->whereNull('currency_id')->update(['currency_id' => $phpId]);
        DB::table('product_orders')->whereNull('currency_id')->update(['currency_id' => $phpId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: we don't want to null out currency_id on rollback
    }
};
