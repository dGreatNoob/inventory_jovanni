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
        Schema::table('products', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('products', 'entity_id')) {
                $table->unsignedBigInteger('entity_id')->default(1)->after('id');
            }
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->unique()->nullable()->after('sku');
            }
            if (!Schema::hasColumn('products', 'specs')) {
                $table->json('specs')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->after('specs');
            }
            if (!Schema::hasColumn('products', 'remarks')) {
                $table->text('remarks')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('products', 'uom')) {
                $table->string('uom')->default('pcs')->after('remarks');
            }
            if (!Schema::hasColumn('products', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->after('uom');
            }
            if (!Schema::hasColumn('products', 'supplier_code')) {
                $table->string('supplier_code')->nullable()->after('supplier_id');
            }
            if (!Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 15, 2)->after('supplier_code');
            }
            if (!Schema::hasColumn('products', 'price_note')) {
                $table->text('price_note')->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'cost')) {
                $table->decimal('cost', 15, 2)->after('price_note');
            }
            if (!Schema::hasColumn('products', 'shelf_life_days')) {
                $table->integer('shelf_life_days')->nullable()->after('cost');
            }
            if (!Schema::hasColumn('products', 'pict_name')) {
                $table->string('pict_name')->nullable()->after('shelf_life_days');
            }
            if (!Schema::hasColumn('products', 'disabled')) {
                $table->boolean('disabled')->default(false)->after('pict_name');
            }
            if (!Schema::hasColumn('products', 'created_by')) {
                $table->unsignedBigInteger('created_by')->after('disabled');
            }
            if (!Schema::hasColumn('products', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
            
            // Add soft deletes if not exists
            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'entity_id',
                'barcode',
                'specs',
                'category_id',
                'remarks',
                'uom',
                'supplier_id',
                'supplier_code',
                'price',
                'price_note',
                'cost',
                'shelf_life_days',
                'pict_name',
                'disabled',
                'created_by',
                'updated_by',
                'deleted_at'
            ]);
        });
    }
};