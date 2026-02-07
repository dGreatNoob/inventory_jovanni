<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('branch_customer_sales', function (Blueprint $table) {
            $table->string('status', 20)->default('completed')->after('total_amount');
        });

        DB::table('branch_customer_sales')->update(['status' => 'completed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_customer_sales', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
