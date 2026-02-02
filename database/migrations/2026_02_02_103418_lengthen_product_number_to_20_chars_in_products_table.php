<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Lengthen product_number to 20 chars and drop unique constraint
     * to support full values from CSV (e.g. K705-38PB, LD2505-128).
     */
    public function up(): void
    {
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->dropUnique('products_product_number_unique');
            });
        } catch (\Throwable $e) {
            // Index may not exist (e.g. SQLite uses different naming)
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE products MODIFY product_number VARCHAR(20) NULL');
        }
        // SQLite: column length is flexible, no change needed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE products MODIFY product_number VARCHAR(6) NULL');
        }

        Schema::table('products', function (Blueprint $table) {
            $table->unique('product_number', 'products_product_number_unique');
        });
    }
};
