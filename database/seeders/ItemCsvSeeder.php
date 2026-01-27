<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\ProductColor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Database\Seeders\SupplierCsvSeeder;

class ItemCsvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if supplier mapping exists (suppliers should be imported first)
        if (empty(SupplierCsvSeeder::$supplierIdMapping)) {
            $this->command->warn("Warning: Supplier ID mapping is empty. Make sure to run SupplierCsvSeeder first!");
            $this->command->warn("Run: php artisan db:seed --class=SupplierCsvSeeder");
        } else {
            $this->command->info("Found " . count(SupplierCsvSeeder::$supplierIdMapping) . " supplier ID mappings");
        }

        $csvPath = base_path('backups_csv/item.csv');

        if (!File::exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            return;
        }

        $this->command->info("Reading CSV file: {$csvPath}");

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

        $rows = [];
        $lineNumber = 0;
        $skipped = 0;
        $processed = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $lineNumber++;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    $skipped++;
                    continue;
                }

                // CSV columns mapping:
                // 0: id (ignore, use auto-increment)
                // 1: created_at
                // 2: created_by
                // 3: updated_at
                // 4: updated_by
                // 5: supplier_id
                // 6: product_number (e.g., "LD2501-81")
                // 7: color_name (e.g., "APRICOT", "BEIGE", "KHAKI", or empty)
                // 8: name (e.g., "Shoulder Bag", "RADIO", "STOVE")
                // 9: sku (can be empty)
                // 10: barcode (can be empty)
                // 11: uom (e.g., "PC")
                // 12: category_id
                // 13: supplier_code
                // 14: price
                // 15: price_note (e.g., "SAL1")
                // 16: cost
                // 17: remarks/weight (e.g., "WT:3599.00 ", "WT:2400.00", " ")
                // 18: disabled (0 or 1)
                // 19: pict_name (image filename, can be empty)
                // 20: NULL

                if (count($row) < 19) {
                    $skipped++;
                    $this->command->warn("Line {$lineNumber}: Insufficient columns (expected at least 19, got " . count($row) . ")");
                    continue;
                }

                // Extract and clean data
                $oldId = trim($row[0] ?? '');
                $createdAt = $this->parseDateTime(trim($row[1] ?? ''));
                $createdBy = $this->parseInt(trim($row[2] ?? ''));
                $updatedAt = $this->parseDateTime(trim($row[3] ?? ''));
                $updatedBy = $this->parseInt(trim($row[4] ?? ''));
                $supplierId = $this->parseInt(trim($row[5] ?? ''));
                $productNumberRaw = trim($row[6] ?? '');
                $colorName = trim($row[7] ?? '');
                $name = trim($row[8] ?? '');
                $sku = trim($row[9] ?? '');
                $barcode = trim($row[10] ?? '');
                $uom = trim($row[11] ?? 'PC');
                $categoryId = $this->parseInt(trim($row[12] ?? ''));
                $supplierCode = trim($row[13] ?? '');
                $price = $this->parseDecimal(trim($row[14] ?? '0'));
                $priceNote = trim($row[15] ?? '');
                $cost = $this->parseDecimal(trim($row[16] ?? '0'));
                $remarks = trim($row[17] ?? '');
                $disabled = $this->parseBoolean(trim($row[18] ?? '0'));
                $pictName = trim($row[19] ?? '');

                // Validate required fields
                if (empty($name)) {
                    $skipped++;
                    $this->command->warn("Line {$lineNumber}: Missing product name");
                    continue;
                }

                if (empty($supplierId)) {
                    $skipped++;
                    $this->command->warn("Line {$lineNumber}: Missing supplier_id");
                    continue;
                }

                if (empty($categoryId)) {
                    $skipped++;
                    $this->command->warn("Line {$lineNumber}: Missing category_id");
                    continue;
                }

                // Check if supplier exists - try mapping first
                $mappedSupplierId = SupplierCsvSeeder::$supplierIdMapping[$supplierId] ?? null;
                $finalSupplierId = $mappedSupplierId ?: $supplierId;
                
                $supplier = Supplier::find($finalSupplierId);
                
                // If not found and mapping exists, the supplier wasn't imported
                // If not found and no mapping, try to find by old ID (in case it matches)
                if (!$supplier && !$mappedSupplierId) {
                    // Try to find by the old ID directly (in case auto-increment matched)
                    $supplier = Supplier::find($supplierId);
                }
                
                if (!$supplier) {
                    $skipped++;
                    $this->command->warn("Line {$lineNumber}: Supplier ID {$supplierId} not found. Please run SupplierCsvSeeder first to import suppliers from backups_csv/supplier.csv");
                    continue;
                }
                
                // Use the mapped/new supplier ID
                $supplierId = $supplier->id;

                // Check if category exists
                $category = Category::find($categoryId);
                if (!$category) {
                    $skipped++;
                    $this->command->warn("Line {$lineNumber}: Category ID {$categoryId} not found");
                    continue;
                }

                // Handle product_number - extract from format like "LD2501-81"
                // Note: product_number has a 6-character limit and unique constraint
                $productNumber = $this->extractProductNumber($productNumberRaw);
                
                // If product_number exceeds 6 characters, truncate it
                if ($productNumber && strlen($productNumber) > 6) {
                    $productNumber = substr($productNumber, 0, 6);
                }
                
                // Check if product_number already exists (unique constraint)
                // Since product_number must be unique, if it already exists, set to null
                if ($productNumber && Product::where('product_number', $productNumber)->exists()) {
                    // Product number already exists, set to null to avoid unique constraint violation
                    $productNumber = null;
                }

                // Handle ProductColor - find or create by name
                $productColorId = null;
                if (!empty($colorName)) {
                    $color = ProductColor::firstOrCreate(
                        ['name' => $colorName],
                        [
                            'code' => $this->generateColorCode($colorName),
                            'name' => $colorName,
                        ]
                    );
                    $productColorId = $color->id;
                }

                // Handle SKU - if empty, generate one or make nullable
                $finalSku = !empty($sku) ? $sku : null;

                // Handle barcode - if empty, make nullable
                $finalBarcode = !empty($barcode) ? $barcode : null;

                // Handle UOM - default to 'pcs' if empty
                $finalUom = !empty($uom) ? strtoupper($uom) : 'PC';

                // Handle created_by and updated_by - use system user if not found
                $finalCreatedBy = $createdBy && \App\Models\User::find($createdBy) ? $createdBy : 1;
                $finalUpdatedBy = $updatedBy && \App\Models\User::find($updatedBy) ? $updatedBy : null;

                // Prepare product data
                $productData = [
                    'entity_id' => 1,
                    'product_number' => $productNumber,
                    'product_color_id' => $productColorId,
                    'product_type' => 'regular',
                    'sku' => $finalSku,
                    'barcode' => $finalBarcode,
                    'name' => $name,
                    'category_id' => $categoryId,
                    'remarks' => !empty($remarks) ? $remarks : null,
                    'uom' => $finalUom,
                    'supplier_id' => $supplierId,
                    'supplier_code' => !empty($supplierCode) ? $supplierCode : null,
                    'price' => $price,
                    'original_price' => $price, // Use price as original_price if not specified
                    'price_note' => !empty($priceNote) ? $priceNote : null,
                    'cost' => $cost,
                    'pict_name' => !empty($pictName) ? $pictName : null,
                    'disabled' => $disabled,
                    'created_by' => $finalCreatedBy,
                    'updated_by' => $finalUpdatedBy,
                ];

                // Set timestamps if provided
                if ($createdAt) {
                    $productData['created_at'] = $createdAt;
                }
                if ($updatedAt) {
                    $productData['updated_at'] = $updatedAt;
                }

                // Check if product already exists (by SKU or barcode or product_number)
                $existingProduct = null;
                if ($finalSku) {
                    $existingProduct = Product::where('sku', $finalSku)->first();
                }
                if (!$existingProduct && $finalBarcode) {
                    $existingProduct = Product::where('barcode', $finalBarcode)->first();
                }
                if (!$existingProduct && $productNumber) {
                    $existingProduct = Product::where('product_number', $productNumber)
                        ->where('product_color_id', $productColorId)
                        ->first();
                }

                if ($existingProduct) {
                    // Update existing product
                    $existingProduct->update($productData);
                    $this->command->info("Line {$lineNumber}: Updated product - {$name} (ID: {$existingProduct->id})");
                } else {
                    // Create new product
                    $product = Product::create($productData);
                    $this->command->info("Line {$lineNumber}: Created product - {$name} (ID: {$product->id})");
                }

                $processed++;

                // Progress indicator
                if ($processed % 100 === 0) {
                    $this->command->info("Processed {$processed} products...");
                }
            }

            fclose($handle);

            DB::commit();

            $this->command->info("\n=== Import Summary ===");
            $this->command->info("Total lines processed: {$lineNumber}");
            $this->command->info("Products imported/updated: {$processed}");
            $this->command->info("Lines skipped: {$skipped}");

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            $this->command->error("Error importing CSV: " . $e->getMessage());
            $this->command->error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Extract product number from format like "LD2501-81" or "TY2506-932"
     * Returns the part before the dash, or the whole string if no dash
     */
    private function extractProductNumber(string $raw): ?string
    {
        if (empty($raw)) {
            return null;
        }

        // If contains dash, take the part before it
        if (strpos($raw, '-') !== false) {
            $parts = explode('-', $raw);
            return strtoupper(trim($parts[0]));
        }

        return strtoupper(trim($raw));
    }

    /**
     * Generate a color code from color name
     */
    private function generateColorCode(string $colorName): string
    {
        // Try to find existing color with same name to get its code
        $existingColor = ProductColor::where('name', $colorName)->first();
        if ($existingColor) {
            return $existingColor->code;
        }

        // Generate a simple numeric code based on name hash
        $hash = crc32(strtoupper($colorName));
        $code = str_pad((string) (abs($hash) % 10000), 4, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        $counter = 1;
        while (ProductColor::where('code', $code)->exists()) {
            $code = str_pad((string) ((abs($hash) + $counter) % 10000), 4, '0', STR_PAD_LEFT);
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

    /**
     * Parse decimal
     */
    private function parseDecimal(?string $value): float
    {
        if (empty($value) || $value === 'NULL' || $value === 'null') {
            return 0.0;
        }

        $value = trim($value);
        if ($value === '') {
            return 0.0;
        }

        // Remove any non-numeric characters except decimal point
        $value = preg_replace('/[^0-9.]/', '', $value);

        return (float) $value;
    }

    /**
     * Parse boolean
     */
    private function parseBoolean(?string $value): bool
    {
        if (empty($value)) {
            return false;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['1', 'true', 'yes', 'on']);
    }
}
