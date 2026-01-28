<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // In some environments the quotation column may not exist yet (fresh databases, CI)
            if (! Schema::hasColumn('purchase_orders', 'quotation')) {
                // Create the column as nullable if it doesn't exist
                $table->string('quotation')->nullable();
            } else {
                // Otherwise, just make it nullable
                $table->string('quotation')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'quotation')) {
                // Revert to non-nullable only if the column exists
                $table->string('quotation')->nullable(false)->change();
            }
        });
    }
};