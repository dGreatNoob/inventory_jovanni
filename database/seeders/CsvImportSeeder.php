<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CsvImportSeeder extends Seeder
{
    /**
     * Run CSV import seeders in the correct order.
     * Prerequisites: CategorySeeder, ProductColorSeeder (for ItemCsvSeeder).
     * Run order: CategorySeeder -> ProductColorSeeder -> SupplierCsvSeeder -> LocationCsvSeeder -> ItemCsvSeeder
     *
     * Usage: php artisan db:seed --class=CsvImportSeeder
     * Production: php artisan db:seed --class=CsvImportSeeder --force
     *
     * Requires backups_csv/ with supplier.csv, location.csv, item.csv in project root.
     */
    public function run(): void
    {
        // Production guard: require --force to run in production
        if (app()->environment('production')) {
            $force = $this->command?->option('force') ?? false;
            if (!$force) {
                throw new \RuntimeException(
                    'CSV import is disabled in production. Run with --force to override: php artisan db:seed --class=CsvImportSeeder --force'
                );
            }
        }

        $this->call([
            CategorySeeder::class,
            ProductColorSeeder::class,
            SupplierCsvSeeder::class,
            LocationCsvSeeder::class,
            ItemCsvSeeder::class,
        ]);
    }
}
