<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY COLUMN shipping_status ENUM('approved', 'failed', 'returned', 'processing', 'pending', 'ready', 'shipped', 'in_transit', 'delivered', 'incomplete', 'damaged', 'cancelled', 'completed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY COLUMN shipping_status ENUM('approved', 'failed', 'returned', 'processing', 'pending', 'ready', 'shipped', 'in_transit', 'delivered', 'incomplete', 'damaged', 'cancelled') DEFAULT 'pending'");
    }
};
