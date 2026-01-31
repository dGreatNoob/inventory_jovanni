<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename subclass1-4 to selling_area1-4, pull_out_addresse to pull_out_address.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->renameColumn('subclass1', 'selling_area1');
            $table->renameColumn('subclass2', 'selling_area2');
            $table->renameColumn('subclass3', 'selling_area3');
            $table->renameColumn('subclass4', 'selling_area4');
            $table->renameColumn('pull_out_addresse', 'pull_out_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->renameColumn('selling_area1', 'subclass1');
            $table->renameColumn('selling_area2', 'subclass2');
            $table->renameColumn('selling_area3', 'subclass3');
            $table->renameColumn('selling_area4', 'subclass4');
            $table->renameColumn('pull_out_address', 'pull_out_addresse');
        });
    }
};
