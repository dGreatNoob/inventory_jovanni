<?php

namespace App\Http\Controllers;

use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DeliveryReceiptController extends Controller
{
    /**
     * Ensure string is properly UTF-8 encoded
     */
    private function ensureUtf8($string)
    {
        if (is_null($string)) {
            return '';
        }

        // Convert to string if not already
        $string = (string) $string;

        // Handle different encodings
        if (!mb_check_encoding($string, 'UTF-8')) {
            // Try to detect encoding and convert
            $encoding = mb_detect_encoding($string, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $string = mb_convert_encoding($string, 'UTF-8', $encoding);
            } else {
                // If detection fails, force UTF-8
                $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
            }
        }

        return $string;
    }

    public function generateDeliveryReceipt($branchAllocationId)
    {
        try {
            $branchAllocation = BranchAllocation::with([
                'branch',
                'batchAllocation',
                'items.product'
            ])->find($branchAllocationId);

            if (!$branchAllocation) {
                return response()->json(['error' => 'Branch allocation not found'], 404);
            }

            // Calculate totals with proper encoding
            $totalItems = 0;
            $totalQuantity = 0;
            $totalPrice = 0;

            $formattedItems = [];

            foreach ($branchAllocation->items as $item) {
                $quantity = $item->quantity ?? 0;
                $unitPrice = $item->product->price ?? $item->product->selling_price ?? 0;
                $totalPricePerItem = $quantity * $unitPrice;

                $totalItems++;
                $totalQuantity += $quantity;
                $totalPrice += $totalPricePerItem;

                $formattedItems[] = [
                    'product_description' => $this->ensureUtf8($item->product->remarks ?? $item->product->name ?? 'Unknown Product'),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPricePerItem,
                ];
            }

            $data = [
                'branch' => $branchAllocation->branch,
                'batch' => $branchAllocation->batchAllocation,
                'items' => $formattedItems,
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
                'total_price' => $totalPrice,
                'generated_date' => now()->format('M d, Y'),
                'generated_time' => now()->format('H:i:s'),
            ];

            // Create PDF with proper encoding
            $pdf = Pdf::loadView('pdfs.delivery-receipt', $data);

            // Set PDF options to handle encoding
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isFontSubsettingEnabled' => true,
            ]);

            // Generate filename with UTF-8 handling
            $branchName = $this->ensureUtf8($branchAllocation->branch->name);
            $refNo = $this->ensureUtf8($branchAllocation->batchAllocation->ref_no);
            $filename = "delivery_receipt_{$branchName}_{$refNo}_" . now()->format('Y-m-d_H-i-s') . '.pdf';

            // Clean filename from special characters
            $filename = preg_replace('/[^\w\-_.]/', '_', $filename);

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate delivery receipt: ' . $e->getMessage()], 500);
        }
    }

    public function previewDeliveryReceipt($branchAllocationId)
    {
        try {
            $branchAllocation = BranchAllocation::with([
                'branch',
                'batchAllocation',
                'items.product'
            ])->find($branchAllocationId);

            if (!$branchAllocation) {
                return response()->json(['error' => 'Branch allocation not found'], 404);
            }

            // Calculate totals with proper encoding
            $totalItems = 0;
            $totalQuantity = 0;
            $totalPrice = 0;

            $formattedItems = [];

            foreach ($branchAllocation->items as $item) {
                $quantity = $item->quantity ?? 0;
                $unitPrice = $item->product->price ?? $item->product->selling_price ?? 0;
                $totalPricePerItem = $quantity * $unitPrice;

                $totalItems++;
                $totalQuantity += $quantity;
                $totalPrice += $totalPricePerItem;

                $formattedItems[] = [
                    'product_description' => $this->ensureUtf8($item->product->remarks ?? $item->product->name ?? 'Unknown Product'),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPricePerItem,
                ];
            }

            $data = [
                'branch' => $branchAllocation->branch,
                'batch' => $branchAllocation->batchAllocation,
                'items' => $formattedItems,
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
                'total_price' => $totalPrice,
                'generated_date' => now()->format('M d, Y'),
                'generated_time' => now()->format('H:i:s'),
            ];

            // Create PDF with proper encoding
            $pdf = Pdf::loadView('pdfs.delivery-receipt', $data);

            // Set PDF options to handle encoding
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isFontSubsettingEnabled' => true,
            ]);

            // Generate filename with UTF-8 handling
            $branchName = $this->ensureUtf8($branchAllocation->branch->name);
            $refNo = $this->ensureUtf8($branchAllocation->batchAllocation->ref_no);
            $filename = "delivery_receipt_{$branchName}_{$refNo}_" . now()->format('Y-m-d_H-i-s') . '.pdf';

            // Clean filename from special characters
            $filename = preg_replace('/[^\w\-_.]/', '_', $filename);

            return $pdf->stream($filename);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to preview delivery receipt: ' . $e->getMessage()], 500);
        }
    }
}