<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_branch_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_code')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('released_at')->nullable(); // null if currently assigned
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_branch_assignments');
    }
};
