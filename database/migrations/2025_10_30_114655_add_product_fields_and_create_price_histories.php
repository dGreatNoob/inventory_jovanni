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
        // Add new fields to products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type', 20)->default('regular')->index()->after('name');
            }
            if (!Schema::hasColumn('products', 'price_levels')) {
                $table->json('price_levels')->nullable()->after('price_note');
            }
            if (!Schema::hasColumn('products', 'discount_tiers')) {
                $table->json('discount_tiers')->nullable()->after('price_levels');
            }
        });

        // Create price history table
        Schema::create('product_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('old_price', 15, 2)->nullable();
            $table->decimal('new_price', 15, 2);
            $table->string('pricing_note', 20)->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->index(['product_id', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_histories');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'discount_tiers')) {
                $table->dropColumn('discount_tiers');
            }
            if (Schema::hasColumn('products', 'price_levels')) {
                $table->dropColumn('price_levels');
            }
            if (Schema::hasColumn('products', 'product_type')) {
                $table->dropColumn('product_type');
            }
        });
    }
};
