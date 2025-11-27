<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drops legacy/commented out module tables that are not actively used.
     * These modules exist in code but are commented out in the sidebar navigation.
     * 
     * Tables being removed:
     * - Paper Roll Warehouse: raw_mat_profiles, raw_mat_orders, raw_mat_invs
     * - Request Slip: request_slips
     * - Sales Return: sales_returns, sales_return_items
     * - Sales Price: sales_prices
     * - Sales Order Branch Items: sales_order_branch_items
     */
    public function up(): void
    {
        // Drop tables in order (child tables first, then parent tables)
        // Using dropIfExists for idempotency - safe to run multiple times
        
        // 1. Drop raw_mat_invs first (references raw_mat_orders)
        Schema::dropIfExists('raw_mat_invs');

        // 2. Drop raw_mat_orders (references raw_mat_profiles)
        Schema::dropIfExists('raw_mat_orders');

        // 3. Drop raw_mat_profiles
        Schema::dropIfExists('raw_mat_profiles');

        // 4. Drop sales_return_items first (references sales_returns)
        Schema::dropIfExists('sales_return_items');

        // 5. Drop sales_returns
        Schema::dropIfExists('sales_returns');

        // 6. Drop sales_order_branch_items
        Schema::dropIfExists('sales_order_branch_items');

        // 7. Drop request_slips
        Schema::dropIfExists('request_slips');

        // 8. Drop sales_prices
        Schema::dropIfExists('sales_prices');
    }

    /**
     * Reverse the migrations.
     * 
     * Recreates the dropped tables with their original structure.
     * Note: This will recreate empty tables - data will be lost.
     */
    public function down(): void
    {
        // Recreate sales_prices
        if (!Schema::hasTable('sales_prices')) {
            Schema::create('sales_prices', function (Blueprint $table) {
                $table->id();
                $table->string('pricing_note')->nullable();
                $table->timestamps();
            });
        }

        // Recreate request_slips
        if (!Schema::hasTable('request_slips')) {
            Schema::create('request_slips', function (Blueprint $table) {
                $table->id();
                $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
                $table->foreignId('sent_from')->constrained('departments')->onDelete('cascade');
                $table->foreignId('sent_to')->constrained('departments')->onDelete('cascade');
                $table->string('purpose')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->foreignId('approver')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // Recreate sales_order_branch_items
        if (!Schema::hasTable('sales_order_branch_items')) {
            Schema::create('sales_order_branch_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
                $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->integer('quantity')->default(0);
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        // Recreate sales_returns
        if (!Schema::hasTable('sales_returns')) {
            Schema::create('sales_returns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->date('return_date');
                $table->string('return_reference')->unique();
                $table->enum('status', ['pending', 'approved', 'rejected', 'processed'])->default('pending');
                $table->text('reason')->nullable();
                $table->decimal('total_refund', 10, 2)->default(0);
                $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // Recreate sales_return_items
        if (!Schema::hasTable('sales_return_items')) {
            Schema::create('sales_return_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_return_id')->constrained('sales_returns')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('supply_profiles')->onDelete('cascade');
                $table->integer('quantity')->default(0);
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->decimal('total_price', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        // Recreate raw_mat_profiles
        if (!Schema::hasTable('raw_mat_profiles')) {
            Schema::create('raw_mat_profiles', function (Blueprint $table) {
                $table->id();
                $table->string('gsm')->nullable();
                $table->integer('width_size')->nullable();
                $table->string('classification')->nullable();
                $table->string('supplier')->nullable();
                $table->string('country_origin')->nullable();
                $table->timestamps();
            });
        }

        // Recreate raw_mat_orders
        if (!Schema::hasTable('raw_mat_orders')) {
            Schema::create('raw_mat_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
                $table->foreignId('raw_mat_profile_id')->constrained('raw_mat_profiles')->onDelete('cascade');
                $table->integer('quantity')->default(0);
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->decimal('total_price', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        // Recreate raw_mat_invs
        if (!Schema::hasTable('raw_mat_invs')) {
            Schema::create('raw_mat_invs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('raw_mat_order_id')->constrained('raw_mat_orders')->onDelete('cascade');
                $table->string('spc_num')->nullable();
                $table->string('supplier_num')->nullable();
                $table->decimal('weight', 10, 2)->nullable();
                $table->decimal('rem_weight', 10, 2)->nullable();
                $table->text('remarks')->nullable();
                $table->text('comment')->nullable();
                $table->date('date_delivered')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }
    }
};
