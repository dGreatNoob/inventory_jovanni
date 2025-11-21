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
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('batch_allocation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_allocation_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['batch_allocation_id']);
            $table->dropForeign(['branch_allocation_id']);
            $table->dropColumn(['batch_allocation_id', 'branch_allocation_id']);
        });
    }
};
