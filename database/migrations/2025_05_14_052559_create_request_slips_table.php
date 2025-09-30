<?php

use App\Models\Department;
use App\Models\User;
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
        Schema::create('request_slips', function (Blueprint $table) {
            $table->id();
            $table->enum('status',[
                'pending',
                'approved',
                'rejected',
            ])->default('pending')->nullable();
            $table->string('purpose')->nullable();
            $table->text( 'description');
            $table->date('request_date');
            $table->foreignIdFor(Department::class, 'sent_from')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->foreignIdFor(Department::class, 'sent_to')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
         
            $table->foreignIdFor(User::class, 'requested_by')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->foreignIdFor(User::class, 'approver')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_slips');
    }
};
