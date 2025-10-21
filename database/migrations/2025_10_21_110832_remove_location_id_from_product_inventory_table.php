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
        // Check if the column exists before trying to drop it
        if (Schema::hasColumn('product_inventory', 'location_id')) {
            Schema::table('product_inventory', function (Blueprint $table) {
                // Drop foreign key first if it exists (must be done before dropping index)
                try {
                    $table->dropForeign(['location_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                
                // Drop the index if it exists
                try {
                    $table->dropIndex('product_inventory_location_id_available_quantity_index');
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }
                
                // Drop the column
                $table->dropColumn('location_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_inventory', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('inventory_locations')->onDelete('cascade');
        });
    }
};
