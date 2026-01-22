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
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Change po_num from bigint to string (VARCHAR)
            // MySQL will automatically convert existing integer values to strings
            $table->string('po_num')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Note: Converting back to bigint may fail if po_num contains non-numeric strings
            // This is a one-way migration in practice, but we provide the rollback for completeness
            $table->bigInteger('po_num')->change();
        });
    }
};
