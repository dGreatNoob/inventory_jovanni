<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class LocationCsvSeeder extends Seeder
{
    /**
     * Mapping of old location IDs to new branch IDs.
     * Use for future FK references (e.g. if other CSVs reference location_id).
     */
    public static array $locationIdMapping = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('backups_csv/location.csv');

        if (!File::exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            return;
        }

        $this->command->info("Reading location CSV file: {$csvPath}");

        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            $this->command->error("Could not open CSV file: {$csvPath}");
            return;
        }

        // Skip BOM if present
        $firstLine = fgets($handle);
        if (substr($firstLine, 0, 3) === "\xef\xbb\xbf") {
            fseek($handle, 3);
        } else {
            rewind($handle);
        }

        $lineNumber = 0;
        $processed = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $lineNumber++;

                if (empty(array_filter($row))) {
                    $skipped++;
                    continue;
                }

                // CSV columns: 0=old_id, 1-4=meta, 5=entity_id?, 6=name, 7=code, 8=category, 9=address, 10=date?, 11=remarks
                if (count($row) < 8) {
                    $skipped++;
                    $this->command->warn("Line {$lineNumber}: Insufficient columns");
                    continue;
                }

                $oldId = trim($row[0] ?? '');
                $name = trim($row[6] ?? '');
                $code = trim($row[7] ?? '');
                $category = trim($row[8] ?? '');
                $address = trim($row[9] ?? '');
                $remarks = trim($row[11] ?? '');

                if (empty($name)) {
                    $skipped++;
                    $this->command->warn("Line {$lineNumber}: Missing location name");
                    continue;
                }

                // Ensure unique code - append suffix if duplicate
                $finalCode = !empty($code) ? $code : 'LOC-' . ($oldId ?: $lineNumber);
                $originalCode = $finalCode;
                $counter = 1;
                while (Branch::where('code', $finalCode)->exists()) {
                    $finalCode = $originalCode . '-' . $counter;
                    $counter++;
                }

                $branchData = [
                    'name' => $name,
                    'code' => $finalCode,
                    'category' => !empty($category) ? $category : 'Imported',
                    'address' => !empty($address) ? $address : '-',
                    'remarks' => !empty($remarks) ? $remarks : null,
                ];

                $branch = Branch::updateOrCreate(
                    ['code' => $finalCode],
                    $branchData
                );

                if ($oldId) {
                    self::$locationIdMapping[$oldId] = $branch->id;
                }

                $processed++;
                if ($processed % 50 === 0) {
                    $this->command->info("Processed {$processed} locations...");
                }
            }

            fclose($handle);
            DB::commit();

            $this->command->info("\n=== Location Import Summary ===");
            $this->command->info("Total lines: {$lineNumber}");
            $this->command->info("Branches imported/updated: {$processed}");
            $this->command->info("Lines skipped: {$skipped}");
            $this->command->info("ID mapping: " . count(self::$locationIdMapping) . " entries");
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            $this->command->error("Error: " . $e->getMessage());
            throw $e;
        }
    }
}
