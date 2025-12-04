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
        Schema::table('branch_allocation_items', function (Blueprint $table) {
            $table->foreignId('box_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('delivery_receipt_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_allocation_items', function (Blueprint $table) {
            $table->dropForeign(['box_id']);
            $table->dropColumn('box_id');
            $table->dropForeign(['delivery_receipt_id']);
            $table->dropColumn('delivery_receipt_id');
        });
    }
};
