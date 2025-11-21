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
        if (Schema::hasTable('purchase_orders')) {
            return;
        }

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
                'to_receive',     // Added for stock-in workflow
                'for_delivery',   // Added for stock-in workflow  
                'received',       // Added for stock-in
                'partial',        // Added for stock-in  
                'damaged',        // Added for stock-in
                'incomplete'      // Added for stock-in
            ])->default('pending');
            $table->string('po_num')->unique();
            $table->decimal('total_price');
            $table->date('order_date');
            $table->dateTime('del_on')->nullable();
            $table->string('payment_terms');
            $table->decimal('total_qty');
            $table->string('dr_number')->nullable(); // Added DR number for stock-in
            $table->foreignIdFor(User::class, 'ordered_by')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->foreignIdFor(User::class, 'approver')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            
            // ✅ Approval tracking
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // ✅ Cancellation tracking
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('return_reason')->nullable();
            
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