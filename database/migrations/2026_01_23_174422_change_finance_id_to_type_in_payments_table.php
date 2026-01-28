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
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['finance_id']);
            $table->dropColumn('finance_id');
            $table->string('type')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->unsignedBigInteger('finance_id')->nullable()->after('payment_method');
            $table->foreign('finance_id')->references('id')->on('finances')->onDelete('cascade');
        });
    }
};
