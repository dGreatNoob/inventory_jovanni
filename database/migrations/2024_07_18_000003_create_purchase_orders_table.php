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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Department::class, 'del_to')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->foreignIdFor(\App\Models\Supplier::class)
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->enum('status',[
                'pending',
                'approved',
                'rejected',
                'for_delivery',
                'delivered',
                'received',
            ])->default('pending');
            $table->bigInteger('po_num')->unique();
            $table->decimal('total_price');
            $table->date('order_date');
            $table->dateTime('del_on')->nullable();
            $table->string('payment_terms');
            $table->string('quotation');
            $table->decimal('total_est_weight')->nullable();
            $table->enum('po_type',[
                'raw_mats', 
                'supply',
            ]);
            $table->decimal('total_qty');
            $table->foreignIdFor(User::class, 'ordered_by')
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
        Schema::dropIfExists('purchase_orders');
    }
};
