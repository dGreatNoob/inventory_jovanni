<?php

use App\Models\RawMatOrder;
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
        if (Schema::hasTable('raw_mat_invs')) {
            return;
        }

        Schema::create('raw_mat_invs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RawMatOrder::class)->constrained()->onDelete('cascade');
            $table->integer('spc_num');
            $table->string('supplier_num');
            $table->integer('weight');
            $table->integer('rem_weight');
            $table->enum('remarks',[
                'pending',
                'accepted',
                'rejected',
            ])->default('pending');
            $table->string('comment')->nullable();
            
            $table->enum('status',[
                'pending',
                'for_delivery',
                'delivered',
                'received',
            ])->default('pending');
            $table->date('date_delivered')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_mat_invs');
    }
};
