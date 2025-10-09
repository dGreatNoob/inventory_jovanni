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
     * @param string|null $prefix Custom prefix (default: 'PROD')
     * @return string Generated barcode
     */
    public function generateBarcode(?string $prefix = 'PROD'): string
    {
        $attempts = 0;
        $maxAttempts = 100;

        do {
            // Generate barcode with format: PREFIX-YYYYMMDD-XXXXX
            // Example: PROD-20251008-00001
            $date = now()->format('Ymd');
            $sequence = str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
            $barcode = "{$prefix}-{$date}-{$sequence}";

            // Check if barcode already exists
            $exists = Product::where('barcode', $barcode)->exists();
            $attempts++;

            if ($attempts >= $maxAttempts) {
                // Fallback: use timestamp + random
                $barcode = "{$prefix}-" . now()->format('YmdHis') . "-" . rand(100, 999);
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
        // Get the last product barcode that matches the pattern
        $lastProduct = Product::whereNotNull('barcode')
            ->where('barcode', 'like', 'PROD-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastProduct && preg_match('/PROD-\d{8}-(\d{5})/', $lastProduct->barcode, $matches)) {
            $lastSequence = intval($matches[1]);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        $date = now()->format('Ymd');
        $paddedSequence = str_pad($newSequence, 5, '0', STR_PAD_LEFT);

        return "PROD-{$date}-{$paddedSequence}";
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
        // Check if barcode matches expected internal format
        // PROD-YYYYMMDD-XXXXX or ENTx-YYYYMMDD-XXXXX
        $pattern = '/^(PROD|ENT\d+)-\d{8}-\d{5}$/';
        return preg_match($pattern, $barcode) === 1;
    }

    /**
     * Parse barcode to extract information
     * 
     * @param string $barcode
     * @return array
     */
    public function parseBarcode(string $barcode): array
    {
        $parts = explode('-', $barcode);
        
        if (count($parts) !== 3) {
            return [
                'prefix' => null,
                'date' => null,
                'sequence' => null,
                'valid' => false
            ];
        }

        return [
            'prefix' => $parts[0],
            'date' => $parts[1],
            'sequence' => $parts[2],
            'valid' => $this->validateBarcode($barcode)
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

