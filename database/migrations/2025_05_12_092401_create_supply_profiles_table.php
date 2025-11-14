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
        if (Schema::hasTable('supply_profiles')) {
            return;
        }

        Schema::create('supply_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('supply_sku');
            $table->string('supply_item_class');
            $table->foreignIdFor(\App\Models\ItemType::class)->constrained()->onDelete('cascade');
            $table->text('supply_description');
            $table->decimal('supply_qty', 10, 2);
            $table->string('supply_uom');
            $table->decimal('supply_min_qty', 10, 2);
            $table->decimal('supply_price1', 10, 2);
            $table->decimal('supply_price2', 10, 2);
            $table->decimal('supply_price3', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_profiles');
    }
};
