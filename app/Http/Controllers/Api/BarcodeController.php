<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BarcodeService;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BarcodeController extends Controller
{
    protected $barcodeService;

    public function __construct(BarcodeService $barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }

    /**
     * Generate a new barcode
     * 
     * @return JsonResponse
     */
    public function generate(Request $request): JsonResponse
    {
        $prefix = $request->input('prefix', 'PROD');
        $barcode = $this->barcodeService->generateBarcode($prefix);

        return response()->json([
            'success' => true,
            'barcode' => $barcode,
            'message' => 'Barcode generated successfully'
        ]);
    }

    /**
     * Generate a sequential barcode
     * 
     * @return JsonResponse
     */
    public function generateSequential(): JsonResponse
    {
        $barcode = $this->barcodeService->generateSequentialBarcode();

        return response()->json([
            'success' => true,
            'barcode' => $barcode,
            'message' => 'Sequential barcode generated successfully'
        ]);
    }

    /**
     * Generate barcode image
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function generateImage(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string',
            'format' => 'sometimes|in:png,svg',
            'width' => 'sometimes|integer|min:1|max:5',
            'height' => 'sometimes|integer|min:20|max:150'
        ]);

        $barcode = $request->input('barcode');
        $format = $request->input('format', 'png');
        $width = $request->input('width', 2);
        $height = $request->input('height', 50);

        if ($format === 'svg') {
            $image = $this->barcodeService->generateBarcodeSVG($barcode, $width, $height);
        } else {
            $image = $this->barcodeService->generateBarcodePNG($barcode, $width, $height);
        }

        return response()->json([
            'success' => true,
            'barcode' => $barcode,
            'image' => $image,
            'format' => $format
        ]);
    }

    /**
     * Validate barcode format
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $barcode = $request->input('barcode');
        $isValid = $this->barcodeService->validateBarcode($barcode);
        $parsed = $this->barcodeService->parseBarcode($barcode);

        return response()->json([
            'success' => true,
            'barcode' => $barcode,
            'is_valid' => $isValid,
            'parsed' => $parsed
        ]);
    }

    /**
     * Check if barcode exists
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function checkExists(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $barcode = $request->input('barcode');
        $exists = Product::where('barcode', $barcode)->exists();
        
        $product = null;
        if ($exists) {
            $product = Product::where('barcode', $barcode)
                ->with(['category', 'supplier'])
                ->first();
        }

        return response()->json([
            'success' => true,
            'barcode' => $barcode,
            'exists' => $exists,
            'product' => $product
        ]);
    }

    /**
     * Bulk generate barcodes
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkGenerate(Request $request): JsonResponse
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
            'prefix' => 'sometimes|string'
        ]);

        $count = $request->input('count');
        $prefix = $request->input('prefix', 'PROD');
        $barcodes = [];

        for ($i = 0; $i < $count; $i++) {
            $barcodes[] = $this->barcodeService->generateBarcode($prefix);
        }

        return response()->json([
            'success' => true,
            'count' => count($barcodes),
            'barcodes' => $barcodes
        ]);
    }
}

