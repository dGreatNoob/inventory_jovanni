<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds fields for Branch Customer Sales overhaul: transaction_date, term, remarks (header);
     * discount_percent, discount_amount, net_price, promo_id, remarks (items).
     */
    public function up(): void
    {
        Schema::table('branch_customer_sales', function (Blueprint $table) {
            $table->date('transaction_date')->nullable()->after('agent_id')->comment('Manual transaction date');
            $table->string('term')->nullable()->after('transaction_date')->comment('Payment term e.g. cod, net_15');
            $table->text('remarks')->nullable()->after('term');
        });

        Schema::table('branch_customer_sale_items', function (Blueprint $table) {
            $table->decimal('discount_percent', 8, 2)->default(0)->after('unit_price');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_percent');
            $table->decimal('net_price', 15, 2)->nullable()->after('discount_amount')->comment('unit_price - discount_amount');
            $table->foreignId('promo_id')->nullable()->after('net_price')->constrained('promos')->onDelete('set null');
            $table->text('remarks')->nullable()->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_customer_sales', function (Blueprint $table) {
            $table->dropColumn(['transaction_date', 'term', 'remarks']);
        });

        Schema::table('branch_customer_sale_items', function (Blueprint $table) {
            $table->dropForeign(['promo_id']);
            $table->dropColumn(['discount_percent', 'discount_amount', 'net_price', 'remarks']);
        });
    }
};
