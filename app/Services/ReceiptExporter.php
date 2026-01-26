<?php

namespace App\Services;

use App\Models\SalesReceipt;
use App\Models\SalesReceiptItem;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class ReceiptExporter
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

    public function exportToPDF($receiptId)
    {
        $receipt = SalesReceipt::with([
            'branch',
            'batchAllocation',
            'items.product'
        ])->find($receiptId);

        if (!$receipt) {
            throw new \Exception('Receipt not found');
        }

        // Calculate totals with proper encoding
        $totalItems = 0;
        $totalQuantity = 0;
        $totalPrice = 0;
        
        $formattedItems = [];
        
        foreach ($receipt->items as $item) {
            $quantity = $item->received_qty ?? 0;
            $unitPrice = $item->product->price ?? 0;
            $totalPricePerItem = $quantity * $unitPrice;
            
            $totalItems++;
            $totalQuantity += $quantity;
            $totalPrice += $totalPricePerItem;
            
            $formattedItems[] = [
                'product_description' => $this->ensureUtf8($item->product->name ?? 'Unknown Product'),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPricePerItem,
            ];
        }

        $data = [
            'receipt' => $receipt,
            'branch' => $receipt->branch,
            'batch' => $receipt->batchAllocation,
            'items' => $formattedItems,
            'total_items' => $totalItems,
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
            'generated_date' => now()->format('M d, Y'),
            'generated_time' => now()->format('H:i:s'),
        ];

        // Create PDF with proper encoding
        $pdf = Pdf::loadView('pdfs.receipt', $data);
        
        // Set PDF options to handle encoding
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'isFontSubsettingEnabled' => true,
        ]);
        
        // Generate filename with UTF-8 handling
        $branchName = $this->ensureUtf8($receipt->branch->name);
        $refNo = $this->ensureUtf8($receipt->batchAllocation->ref_no);
        $filename = "receipt_{$branchName}_{$refNo}_" . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        // Clean filename from special characters
        $filename = preg_replace('/[^\w\-_.]/', '_', $filename);
        
        return $pdf->download($filename);
    }

    public function exportToExcel($receiptId)
    {
        $receipt = SalesReceipt::with([
            'branch',
            'batchAllocation',
            'items.product'
        ])->find($receiptId);

        if (!$receipt) {
            throw new \Exception('Receipt not found');
        }

        $exportData = [];
        
        // Add header information with proper encoding
        $exportData[] = [
            'Brand' => 'Jovanni Bags',
            'Vendor Code' => '104148',
            'Dept Code' => '041072007',
            'Date' => now()->format('d M Y'),
            'Delivered To' => $this->ensureUtf8($receipt->branch->name),
            'Series #' => $this->ensureUtf8($receipt->batchAllocation->ref_no),
            'Ship Via' => 'SM STA ROSA',
            'Address' => 'SM City Sta. Rosa National Road Tagapo, Sta. Rosa Laguna 4026'
        ];
        
        $exportData[] = []; // Empty row
        
        // Add table headers
        $exportData[] = [
            'Product Description',
            'Qty',
            'Unit Price',
            'Total Price'
        ];
        
        $totalItems = 0;
        $totalQuantity = 0;
        $totalPrice = 0;
        
        foreach ($receipt->items as $item) {
            $quantity = $item->received_qty ?? 0;
            $unitPrice = $item->product->price ?? 0;
            $totalPricePerItem = $quantity * $unitPrice;
            
            $totalItems++;
            $totalQuantity += $quantity;
            $totalPrice += $totalPricePerItem;
            
            $exportData[] = [
                $this->ensureUtf8($item->product->name ?? 'Unknown Product'),
                $quantity,
                number_format($unitPrice, 2),
                number_format($totalPricePerItem, 2)
            ];
        }
        
        // Add totals
        $exportData[] = []; // Empty row
        $exportData[] = [
            'Total item/s:',
            $totalItems,
            '',
            number_format($totalPrice, 2)
        ];
        
        $exportData[] = [
            'Total Quantity:',
            $totalQuantity,
            '',
            ''
        ];
        
        $exportData[] = [
            'Total Price:',
            '',
            '',
            number_format($totalPrice, 2)
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Populate data with proper encoding using array indices
        foreach ($exportData as $rowIndex => $row) {
            if (empty($row)) {
                continue; // Skip empty rows
            }
            
            // Reset column index for each row
            $colIndex = 0;
            
            foreach ($row as $value) {
                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $cellCoordinate = $column . ($rowIndex + 1);
                
                // Set cell value with UTF-8 encoding
                if (is_string($value)) {
                    $value = $this->ensureUtf8($value);
                }
                
                $sheet->setCellValue($cellCoordinate, $value);
                $colIndex++;
            }
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);

        $writer = new Xlsx($spreadsheet);
        
        // Generate filename with UTF-8 handling
        $branchName = $this->ensureUtf8($receipt->branch->name);
        $refNo = $this->ensureUtf8($receipt->batchAllocation->ref_no);
        $filename = "receipt_{$branchName}_{$refNo}_" . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        // Clean filename from special characters
        $filename = preg_replace('/[^\w\-_.]/', '_', $filename);
        
        // Create temporary file and download
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);
        
        return Response::download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}