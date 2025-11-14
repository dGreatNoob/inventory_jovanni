<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\ProductColor;

class ProductColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('jovanni_dbSchema_backup/MASTERFILE-PRODUCT-ID-COLOR-ID.xlsx-COLOR-CODE.csv');

        if (!File::exists($csvPath)) {
            $this->command?->warn("Product color masterfile not found at {$csvPath}. Skipping ProductColorSeeder.");
            return;
        }

        $rows = array_map('str_getcsv', file($csvPath));

        if (empty($rows)) {
            $this->command?->warn('Product color masterfile is empty. Skipping ProductColorSeeder.');
            return;
        }

        // Remove header row
        $header = array_shift($rows);

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                if (empty($row) || empty($row[0]) || empty($row[1])) {
                    continue;
                }

                $rawCode = trim($row[0]);
                if ($rawCode === '') {
                    continue;
                }

                // Normalize code to numeric 4-digit representation
                $digits = preg_replace('/\D/', '', $rawCode);

                if ($digits === '') {
                    continue;
                }

                $normalizedCode = str_pad(substr($digits, 0, 4), 4, '0', STR_PAD_LEFT);

                $name = trim((string) ($row[1] ?? ''));
                $shortcut = trim((string) ($row[2] ?? ''));

                ProductColor::updateOrCreate(
                    ['code' => $normalizedCode],
                    [
                        'name' => $name ?: $normalizedCode,
                        'shortcut' => $shortcut !== '' ? $shortcut : null,
                    ]
                );
            }
        });
    }
}

