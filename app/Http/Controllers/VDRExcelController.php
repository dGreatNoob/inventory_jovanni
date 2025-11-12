<?php

namespace App\Http\Controllers;

use App\Models\BatchAllocation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class VDRExcelController extends Controller
{
    public function exportVDR(Request $request, $batchId)
    {
        $batch = BatchAllocation::with([
            'branchAllocations.branch',
            'branchAllocations.items.product'
        ])->findOrFail($batchId);

        $vendorCode = $request->query('vendor_code', '104148');
        $vendorName = $request->query('vendor_name', 'JKF CORP.');
        $preparedBy = $request->query('prepared_by', '');

        try {
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set sheet title
            $sheet->setTitle('VDR - ' . $batch->ref_no);

            // Add data to the spreadsheet
            $this->addDataToSheet($sheet, $batch, $vendorCode, $vendorName, $preparedBy);

            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            $fileName = 'VDR_' . $batch->ref_no . '_' . now()->format('YmdHis') . '.xlsx';
            
            // Prepare response
            $response = new Response();
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            $response->headers->set('Cache-Control', 'max-age=0');
            $response->headers->set('Cache-Control', 'max-age=1');
            $response->headers->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
            $response->headers->set('Cache-Control', 'cache, must-revalidate');
            $response->headers->set('Pragma', 'public');

            ob_start();
            $writer->save('php://output');
            $excelContent = ob_get_clean();
            
            $response->setContent($excelContent);
            
            return $response;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate Excel file: ' . $e->getMessage());
        }
    }

    private function addDataToSheet($sheet, $batch, $vendorCode, $vendorName, $preparedBy)
    {
        $row = 1;
        
        // Page info
        $sheet->setCellValue('A' . $row, 'PAGE:');
        $sheet->setCellValue('B' . $row, '1 of 1');
        $row += 2;
        
        // Vendor information section
        $sheet->setCellValue('A' . $row, 'STORE CONSIGNOR');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'VALIDATED DELIVERY RECEIPT');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'VENDOR CODE: ' . $vendorCode);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'VENDOR NAME: ' . $vendorName);
        $row += 2;
        
        // Table headers
        $headers = [
            'DR#', 'STORE CODE', 'STORE NAME', 'EXP. DELIVERY DATE (MMDDYY)', 
            'DP', 'SD', 'CL', 'CLASS DESC', 'BOXES', 'SKU #', 'SKU DESC', 'QTY'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE6E6E6');
            $sheet->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $col++;
        }
        $row++;

        $totalQty = 0;
        $totalBoxes = 0;
        $uniqueSkus = collect();

        // Process each branch allocation
        foreach ($batch->branchAllocations as $branchAllocation) {
            $branch = $branchAllocation->branch;
            $storeCode = $branch->code ?? '';
            $storeName = $branch->name ?? '';

            // Process each item
            foreach ($branchAllocation->items as $item) {
                $product = $item->product;
                $skuNumber = $product->sku ?? $product->id;
                $skuDescription = $product->name ?? '';
                
                $sheet->setCellValue('A' . $row, $batch->ref_no);
                $sheet->setCellValue('B' . $row, $storeCode);
                $sheet->setCellValue('C' . $row, $storeName);
                $sheet->setCellValue('D' . $row, \Carbon\Carbon::parse($batch->transaction_date)->format('m/d/y'));
                $sheet->setCellValue('E' . $row, '04'); // Default values
                $sheet->setCellValue('F' . $row, '10');
                $sheet->setCellValue('G' . $row, '72007');
                $sheet->setCellValue('H' . $row, 'JOVANNI');
                $sheet->setCellValue('I' . $row, '0'); // Will be implemented later
                $sheet->setCellValue('J' . $row, $skuNumber);
                $sheet->setCellValue('K' . $row, $skuDescription);
                $sheet->setCellValue('L' . $row, $item->quantity);
                
                // Add borders to all cells
                for ($colIndex = 'A'; $colIndex <= 'L'; $colIndex++) {
                    $sheet->getStyle($colIndex . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                $totalQty += $item->quantity;
                $totalBoxes += 0; // To be implemented later
                $uniqueSkus->push($skuNumber);
                $row++;
            }
        }

        // Add summary rows
        $row += 2; // Empty row
        
        if ($preparedBy) {
            $sheet->setCellValue('A' . $row, 'Prepared By: ' . $preparedBy);
            $row++;
        }
        $row++;
        
        $sheet->setCellValue('A' . $row, 'TOTAL QTY: ' . $totalQty);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'TOTAL BOXES: ' . $totalBoxes);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'TOTAL SKU/S: ' . $uniqueSkus->unique()->count());
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'RUN TIME: ' . now()->format('g:i:s a'));
        $row++;
        
        $sheet->setCellValue('A' . $row, 'RUN DATE: ' . now()->format('m/d/Y'));
        
        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}