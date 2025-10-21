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
        if (Schema::hasColumn('inventory_movements', 'location_id')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                // Drop foreign key if it exists
                try {
                    $table->dropForeign(['location_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
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
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('inventory_locations')->onDelete('cascade');
        });
    }
};
