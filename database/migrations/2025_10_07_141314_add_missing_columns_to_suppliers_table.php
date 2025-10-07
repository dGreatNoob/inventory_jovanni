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
        Schema::table('suppliers', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('suppliers', 'entity_id')) {
                $table->unsignedBigInteger('entity_id')->default(1)->after('id');
            }
            if (!Schema::hasColumn('suppliers', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('name');
            }
            if (!Schema::hasColumn('suppliers', 'email')) {
                $table->string('email')->nullable()->after('contact_person');
            }
            if (!Schema::hasColumn('suppliers', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('suppliers', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('suppliers', 'country')) {
                $table->string('country')->nullable()->after('city');
            }
            if (!Schema::hasColumn('suppliers', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('country');
            }
            if (!Schema::hasColumn('suppliers', 'terms')) {
                $table->text('terms')->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('suppliers', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('terms');
            }
            if (!Schema::hasColumn('suppliers', 'credit_limit')) {
                $table->decimal('credit_limit', 15, 2)->nullable()->after('tax_id');
            }
            if (!Schema::hasColumn('suppliers', 'payment_terms_days')) {
                $table->integer('payment_terms_days')->default(30)->after('credit_limit');
            }
            if (!Schema::hasColumn('suppliers', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('payment_terms_days');
            }
            
            // Add soft deletes if not exists
            if (!Schema::hasColumn('suppliers', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'entity_id',
                'contact_person',
                'email',
                'phone',
                'city',
                'country',
                'postal_code',
                'terms',
                'tax_id',
                'credit_limit',
                'payment_terms_days',
                'is_active',
                'deleted_at'
            ]);
        });
    }
};