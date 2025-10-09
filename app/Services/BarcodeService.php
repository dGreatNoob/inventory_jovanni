<?php

namespace App\Services;

use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeService
{
    /**
     * Generate a unique barcode for a product
     * 
     * @return string Generated barcode (13 digits like 8901234567006)
     */
    public function generateBarcode(): string
    {
        $attempts = 0;
        $maxAttempts = 100;

        do {
            // Generate 13-digit barcode format: 890 + 10 random digits
            // Example: 8901234567006
            $prefix = '890'; // Common prefix for internal use
            $randomDigits = str_pad(random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);
            $barcode = $prefix . $randomDigits;

            // Check if barcode already exists
            $exists = Product::where('barcode', $barcode)->exists();
            $attempts++;

            if ($attempts >= $maxAttempts) {
                // Fallback: use timestamp + random
                $timestamp = now()->format('YmdHis');
                $barcode = '890' . substr($timestamp, 2); // Remove first 2 digits of year
                break;
            }
        } while ($exists);

        return $barcode;
    }

    /**
     * Generate barcode with entity-specific format
     * 
     * @param int $entityId
     * @param int $productId
     * @return string
     */
    public function generateBarcodeWithEntity(int $entityId, int $productId): string
    {
        // Format: ENT{entity}-{date}-{padded_product_id}
        // Example: ENT1-20251008-00001
        $date = now()->format('Ymd');
        $paddedId = str_pad($productId, 5, '0', STR_PAD_LEFT);
        
        return "ENT{$entityId}-{$date}-{$paddedId}";
    }

    /**
     * Generate sequential barcode
     * 
     * @return string
     */
    public function generateSequentialBarcode(): string
    {
        // Get the last product ID to generate sequential barcode
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $lastId = $lastProduct ? $lastProduct->id : 0;
        
        // Generate 13-digit barcode: 890 + padded product ID
        $prefix = '890';
        $paddedId = str_pad($lastId + 1, 10, '0', STR_PAD_LEFT);
        
        return $prefix . $paddedId;
    }

    /**
     * Generate barcode image as PNG
     * 
     * @param string $barcode
     * @param int $widthFactor
     * @param int $height
     * @return string Base64 encoded PNG
     */
    public function generateBarcodePNG(string $barcode, int $widthFactor = 2, int $height = 50): string
    {
        $generator = new BarcodeGeneratorPNG();
        
        try {
            // Generate Code128 barcode (widely supported)
            $barcodeImage = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, $widthFactor, $height);
            return 'data:image/png;base64,' . base64_encode($barcodeImage);
        } catch (\Exception $e) {
            \Log::error('Barcode generation failed: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Generate barcode image as SVG
     * 
     * @param string $barcode
     * @param int $widthFactor
     * @param int $height
     * @param string $color
     * @return string SVG string
     */
    public function generateBarcodeSVG(string $barcode, int $widthFactor = 2, int $height = 50, string $color = 'black'): string
    {
        $generator = new BarcodeGeneratorSVG();
        
        try {
            return $generator->getBarcode($barcode, $generator::TYPE_CODE_128, $widthFactor, $height, $color);
        } catch (\Exception $e) {
            \Log::error('Barcode SVG generation failed: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Validate barcode format
     * 
     * @param string $barcode
     * @return bool
     */
    public function validateBarcode(string $barcode): bool
    {
        // Check if barcode is 13 digits (like 8901234567006)
        return preg_match('/^\d{13}$/', $barcode) === 1;
    }

    /**
     * Parse barcode to extract information
     * 
     * @param string $barcode
     * @return array
     */
    public function parseBarcode(string $barcode): array
    {
        if (!$this->validateBarcode($barcode)) {
            return [
                'prefix' => null,
                'sequence' => null,
                'valid' => false
            ];
        }

        return [
            'prefix' => substr($barcode, 0, 3), // First 3 digits (890)
            'sequence' => substr($barcode, 3),  // Remaining 10 digits
            'valid' => true
        ];
    }

    /**
     * Get barcode type name
     * 
     * @return string
     */
    public function getBarcodeTypeName(): string
    {
        return 'Code 128';
    }

    /**
     * Generate printable barcode label (HTML)
     * 
     * @param Product $product
     * @return string
     */
    public function generateBarcodeLabel(Product $product): string
    {
        $barcodeImage = $this->generateBarcodePNG($product->barcode);
        
        return view('components.barcode-label', [
            'product' => $product,
            'barcodeImage' => $barcodeImage
        ])->render();
    }
}

