<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * - Migrate existing 'pending' suppliers to 'inactive'
     * - Drop tin_num column (no longer collected in add/edit forms)
     */
    public function up(): void
    {
        // Migrate pending status to inactive before removing the option
        DB::table('suppliers')->where('status', 'pending')->update(['status' => 'inactive']);

        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'tin_num')) {
                $table->dropColumn('tin_num');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'tin_num')) {
                $table->string('tin_num')->nullable()->after('email');
            }
        });
    }
};
