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
        Schema::table('finances', function (Blueprint $table) {
            // Only add columns if they don't already exist
            if (!Schema::hasColumn('finances', 'customer')) {
                $table->string('customer')->nullable()->after('purchase_order'); // <-- ADDED FOR RECEIVABLES
            }
            if (!Schema::hasColumn('finances', 'sales_order')) {
                $table->string('sales_order')->nullable()->after('customer'); // <-- ADDED FOR RECEIVABLES
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropColumn('customer'); // <-- ADDED FOR RECEIVABLES
            $table->dropColumn('sales_order'); // <-- ADDED FOR RECEIVABLES
        });
    }
}; 