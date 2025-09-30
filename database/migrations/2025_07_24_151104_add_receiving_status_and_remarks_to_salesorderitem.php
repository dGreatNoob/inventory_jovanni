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
        
        Schema::table('sales_order_items', function (Blueprint $table) {           
            $table->enum('receiving_status', ['good','incomplete','destroyed'])->nullable()->after('subtotal');           
            $table->text('receiving_remarks')->nullable()->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            //
            $table->dropColumn('receiving_status');
            $table->dropColumn('receiving_remarks');
        });
    }
};
