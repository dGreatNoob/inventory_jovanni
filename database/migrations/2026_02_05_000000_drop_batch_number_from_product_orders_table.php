<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_orders')) {
            return;
        }

        Schema::table('product_orders', function (Blueprint $table) {
            // Drop index if it exists, then drop the column
            try {
                $table->dropIndex(['batch_number']);
            } catch (\Throwable $e) {
                // Index name may differ or may not exist; ignore
            }

            if (Schema::hasColumn('product_orders', 'batch_number')) {
                $table->dropColumn('batch_number');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('product_orders')) {
            return;
        }

        Schema::table('product_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('product_orders', 'batch_number')) {
                $table->string('batch_number')->nullable();
                $table->index('batch_number');
            }
        });
    }
};

