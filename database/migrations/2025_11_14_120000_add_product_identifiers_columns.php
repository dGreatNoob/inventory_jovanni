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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'product_number')) {
                $table->string('product_number', 6)
                    ->nullable()
                    ->after('entity_id');

                $table->unique('product_number', 'products_product_number_unique');
            }

            if (!Schema::hasColumn('products', 'color_id')) {
                $table->string('color_id', 4)
                    ->nullable()
                    ->after('product_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'color_id')) {
                $table->dropColumn('color_id');
            }

            if (Schema::hasColumn('products', 'product_number')) {
                $table->dropUnique('products_product_number_unique');
                $table->dropColumn('product_number');
            }
        });
    }
};

