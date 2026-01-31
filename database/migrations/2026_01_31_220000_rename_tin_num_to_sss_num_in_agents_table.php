<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename tin_num to sss_num to avoid confusion with SSS # field.
     */
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->renameColumn('tin_num', 'sss_num');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->renameColumn('sss_num', 'tin_num');
        });
    }
};
