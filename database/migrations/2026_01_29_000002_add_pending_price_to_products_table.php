<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'pending_price')) {
                $table->decimal('pending_price', 15, 2)->nullable()->after('price_effective_date');
            }
            if (!Schema::hasColumn('products', 'pending_price_note')) {
                $table->string('pending_price_note', 20)->nullable()->after('pending_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'pending_price')) {
                $table->dropColumn('pending_price');
            }
            if (Schema::hasColumn('products', 'pending_price_note')) {
                $table->dropColumn('pending_price_note');
            }
        });
    }
};
