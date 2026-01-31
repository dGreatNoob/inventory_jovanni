<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename subclass to selling_area for consistency with branches table.
     */
    public function up(): void
    {
        Schema::table('agent_branch_assignments', function (Blueprint $table) {
            $table->renameColumn('subclass', 'selling_area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_branch_assignments', function (Blueprint $table) {
            $table->renameColumn('selling_area', 'subclass');
        });
    }
};
