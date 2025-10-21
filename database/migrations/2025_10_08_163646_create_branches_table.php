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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subclass1')->nullable();
            $table->string('subclass2')->nullable();
            $table->string('subclass3')->nullable();
            $table->string('subclass4')->nullable();
            $table->string('code');
            $table->string('category');
            $table->string('address');
            $table->string('remarks')->nullable();
            $table->string('batch')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_tin')->nullable();
            $table->string('dept_code')->nullable();
            $table->string('pull_out_addresse')->nullable();
            $table->string('vendor_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};