<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class SupplierCsvSeeder extends Seeder
{
    /**
     * Mapping of old supplier IDs to new supplier IDs
     * This will be populated during seeding
     */
    public static $supplierIdMapping = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('backups_csv/supplier.csv');

        if (!File::exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            return;
        }

        $this->command->info("Reading supplier CSV file: {$csvPath}");

        // Read CSV file
        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            $this->command->error("Could not open CSV file: {$csvPath}");
            return;
        }

        // Skip BOM if present
        $firstLine = fgets($handle);
        if (substr($firstLine, 0, 3) === "\xef\xbb\xbf") {
            $firstLine = substr($firstLine, 3);
        }
        rewind($handle);
        if (substr($firstLine, 0, 3) === "\xef\xbb\xbf") {
            fseek($handle, 3);
        }

        $processed = 0;
        $skipped = 0;
        $lineNumber = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $lineNumber++;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    $skipped++;
                    continue;
                }

                // CSV columns mapping (based on sample data):
                // 0: old_id (the original supplier ID from the old system)
                // 1: created_at
                // 2: created_by
                // 3: updated_at
                // 4: updated_by
                // 5: entity_id
                // 6: name
                // 7: email
                // 8: date (some date field, possibly tin_num or other)
                // 9: remarks/notes

                if (count($row) < 7) {
                    $skipped++;
                    $this->command->warn("Line {$lineNumber}: Insufficient columns (expected at least 7, got " . count($row) . ")");
                    continue;
                }

                // Extract and clean data
                $oldId = trim($row[0] ?? '');
                $createdAt = $this->parseDateTime(trim($row[1] ?? ''));
                $createdBy = $this->parseInt(trim($row[2] ?? ''));
                $updatedAt = $this->parseDateTime(trim($row[3] ?? ''));
                $updatedBy = $this->parseInt(trim($row[4] ?? ''));
                $entityId = $this->parseInt(trim($row[5] ?? '1'));
                $name = trim($row[6] ?? '');
                $email = trim($row[7] ?? '');
                $dateField = trim($row[8] ?? '');
                $remarks = trim($row[9] ?? '');

                // Validate required fields
                if (empty($name)) {
                    $skipped++;
                    $this->command->warn("Line {$lineNumber}: Missing supplier name");
                    continue;
                }

                if (empty($oldId)) {
                    $skipped++;
                    $this->command->warn("Line {$lineNumber}: Missing old supplier ID");
                    continue;
                }

                // Prepare supplier data
                $supplierData = [
                    'entity_id' => $entityId ?: 1,
                    'name' => $name,
                    'email' => !empty($email) ? $email : null,
                    'code' => $this->generateSupplierCode($name, $oldId),
                    'is_active' => true,
                    'status' => 'active',
                ];

                // Set timestamps if provided
                if ($createdAt) {
                    $supplierData['created_at'] = $createdAt;
                }
                if ($updatedAt) {
                    $supplierData['updated_at'] = $updatedAt;
                }

                // Check if supplier already exists by name
                $supplier = Supplier::where('name', $name)->first();

                if ($supplier) {
                    // Update existing supplier
                    $supplier->update($supplierData);
                    $newId = $supplier->id;
                    $this->command->info("Line {$lineNumber}: Updated supplier - {$name} (ID: {$newId})");
                } else {
                    // Create new supplier
                    $supplier = Supplier::create($supplierData);
                    $newId = $supplier->id;
                    $this->command->info("Line {$lineNumber}: Created supplier - {$name} (ID: {$newId})");
                }

                // Store mapping of old ID to new ID
                self::$supplierIdMapping[$oldId] = $newId;

                $processed++;

                // Progress indicator
                if ($processed % 50 === 0) {
                    $this->command->info("Processed {$processed} suppliers...");
                }
            }

            fclose($handle);

            DB::commit();

            $this->command->info("\n=== Supplier Import Summary ===");
            $this->command->info("Total lines processed: {$lineNumber}");
            $this->command->info("Suppliers imported/updated: {$processed}");
            $this->command->info("Lines skipped: {$skipped}");
            $this->command->info("ID Mapping created: " . count(self::$supplierIdMapping) . " entries");

        } catch (\Throwable $e) {
            DB::rollBack();
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            $this->command->error("Error importing supplier CSV: " . $e->getMessage());
            $this->command->error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Generate a supplier code from name and old ID
     */
    private function generateSupplierCode(string $name, string $oldId): ?string
    {
        // Try to create a code from the name
        $code = strtoupper(preg_replace('/[^A-Z0-9]/', '', substr($name, 0, 10)));
        
        if (empty($code)) {
            $code = 'SUP' . str_pad($oldId, 4, '0', STR_PAD_LEFT);
        }

        // Ensure uniqueness
        $counter = 1;
        $originalCode = $code;
        while (Supplier::where('code', $code)->exists()) {
            $code = $originalCode . $counter;
            $counter++;
        }

        return $code;
    }

    /**
     * Parse datetime string
     */
    private function parseDateTime(?string $value): ?Carbon
    {
        if (empty($value) || $value === 'NULL' || $value === 'null') {
            return null;
        }

        try {
            // Handle various datetime formats
            $value = rtrim($value, '.0'); // Remove trailing .0
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse integer
     */
    private function parseInt(?string $value): ?int
    {
        if (empty($value) || $value === 'NULL' || $value === 'null') {
            return null;
        }

        $value = trim($value);
        if ($value === '' || $value === '0') {
            return null;
        }

        return (int) $value;
    }
}
