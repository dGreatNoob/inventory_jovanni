<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CsvImportSeeder extends Seeder
{
    /**
     * Run CSV import seeders in the correct order.
     * Run order: SupplierCsvSeeder -> LocationCsvSeeder -> ItemCsvSeeder
     */
    public function run(): void
    {
        $this->call([
            SupplierCsvSeeder::class,
            LocationCsvSeeder::class,
            ItemCsvSeeder::class,
        ]);
    }
}
