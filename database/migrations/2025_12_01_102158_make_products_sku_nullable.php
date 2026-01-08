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
        // Make SKU column nullable while keeping the unique constraint
        // The unique constraint will still work (NULL values don't conflict with each other)
        DB::statement('ALTER TABLE products MODIFY sku VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Before making it NOT NULL, we need to handle any NULL values
        // Set NULL values to empty string or a unique value
        DB::statement('UPDATE products SET sku = CONCAT("SKU-", id) WHERE sku IS NULL');
        
        // Now make it NOT NULL again
        DB::statement('ALTER TABLE products MODIFY sku VARCHAR(255) NOT NULL');
    }
};
