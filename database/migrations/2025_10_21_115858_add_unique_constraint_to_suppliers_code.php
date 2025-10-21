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
        // First, update any existing suppliers with empty codes
        $suppliers = \App\Models\Supplier::where('code', '')->orWhereNull('code')->get();
        foreach ($suppliers as $supplier) {
            $supplier->update(['code' => 'SUP-' . str_pad($supplier->id, 3, '0', STR_PAD_LEFT)]);
        }
        
        // Add unique constraint
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropUnique(['code']);
        });
    }
};
