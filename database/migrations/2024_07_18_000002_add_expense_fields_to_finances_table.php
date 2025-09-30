<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->string('expense_number')->nullable()->after('id');
            $table->string('description')->nullable()->after('reference_id');
            $table->date('expense_date')->nullable()->after('date');
            $table->unsignedBigInteger('department_id')->nullable()->after('expense_date');
            $table->text('notes')->nullable()->after('remarks');
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['expense_number', 'description', 'expense_date', 'department_id', 'notes']);
        });
    }
}; 