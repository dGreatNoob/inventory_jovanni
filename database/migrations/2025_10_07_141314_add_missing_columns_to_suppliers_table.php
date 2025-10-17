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
            // contact_person already exists - skip
            // email already exists - skip
            
            // Add phone column (maps to contact_num in existing table)
            if (!Schema::hasColumn('suppliers', 'phone') && Schema::hasColumn('suppliers', 'contact_num')) {
                // Rename contact_num to phone for consistency with model
                $table->renameColumn('contact_num', 'phone');
            } elseif (!Schema::hasColumn('suppliers', 'phone')) {
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
            // Only drop columns that were actually added by this migration
            $columnsToDrop = [];
            
            if (Schema::hasColumn('suppliers', 'entity_id')) {
                $columnsToDrop[] = 'entity_id';
            }
            if (Schema::hasColumn('suppliers', 'city')) {
                $columnsToDrop[] = 'city';
            }
            if (Schema::hasColumn('suppliers', 'country')) {
                $columnsToDrop[] = 'country';
            }
            if (Schema::hasColumn('suppliers', 'postal_code')) {
                $columnsToDrop[] = 'postal_code';
            }
            if (Schema::hasColumn('suppliers', 'terms')) {
                $columnsToDrop[] = 'terms';
            }
            if (Schema::hasColumn('suppliers', 'tax_id')) {
                $columnsToDrop[] = 'tax_id';
            }
            if (Schema::hasColumn('suppliers', 'credit_limit')) {
                $columnsToDrop[] = 'credit_limit';
            }
            if (Schema::hasColumn('suppliers', 'payment_terms_days')) {
                $columnsToDrop[] = 'payment_terms_days';
            }
            if (Schema::hasColumn('suppliers', 'is_active')) {
                $columnsToDrop[] = 'is_active';
            }
            
            // Rename phone back to contact_num if it was renamed
            if (Schema::hasColumn('suppliers', 'phone') && !Schema::hasColumn('suppliers', 'contact_num')) {
                $table->renameColumn('phone', 'contact_num');
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
            
            // Drop soft deletes
            if (Schema::hasColumn('suppliers', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};