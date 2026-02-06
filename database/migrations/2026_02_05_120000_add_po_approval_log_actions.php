<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add closed_for_fulfillment and reopened (and returned_to_approved if missing)
     * to purchase_order_approval_logs.action enum.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE purchase_order_approval_logs MODIFY COLUMN action ENUM(
                'approved',
                'rejected',
                'created',
                'delivered',
                'received',
                'returned_to_approved',
                'closed_for_fulfillment',
                'reopened'
            ) DEFAULT 'created'");
        }
        // If not MySQL (e.g. SQLite), the column may be string already; no change needed.
    }

    /**
     * Revert to original enum values (cannot remove if rows use new values).
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE purchase_order_approval_logs MODIFY COLUMN action ENUM(
                'approved',
                'rejected',
                'created',
                'delivered',
                'received'
            ) DEFAULT 'created'");
        }
    }
};
