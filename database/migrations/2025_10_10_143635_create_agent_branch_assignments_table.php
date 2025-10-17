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
            
            // Foreign keys with proper constraints
            $table->foreignId('agent_id')
                  ->constrained('agents')
                  ->onDelete('cascade');
                  
            $table->foreignId('branch_id')
                  ->constrained('branches')
                  ->onDelete('cascade');
            
            $table->string('subclass')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_branch_assignments');
    }
};