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
            if (!Schema::hasColumn('products', 'product_color_id')) {
                $table->foreignId('product_color_id')
                    ->nullable()
                    ->after('product_number')
                    ->constrained('product_colors')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('products', 'color_id')) {
                $table->dropColumn('color_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'product_color_id')) {
                $table->dropConstrainedForeignId('product_color_id');
            }

            if (!Schema::hasColumn('products', 'color_id')) {
                $table->string('color_id', 4)->nullable()->after('product_number');
            }
        });
    }
};

