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

        try {
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set sheet title
            $sheet->setTitle('VDR - ' . $batch->ref_no);

            // Add data to the spreadsheet
            $this->addDataToSheet($sheet, $batch);

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

    private function addDataToSheet($sheet, $batch)
    {
        $row = 1;
        
        // Page info
        $sheet->setCellValue('A' . $row, 'PAGE:');
        $sheet->setCellValue('B' . $row, '1 of 1');
        $row += 2;
        
        // Table headers
        $headers = [
            'DR#', 'STORE CODE', 'STORE NAME', 'EXP. DELIVERY DATE (MMDDYY)',
            'DP', 'SD', 'CL', 'SKU #', 'QTY'
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
                
                $sheet->setCellValue('A' . $row, $batch->ref_no);
                $sheet->setCellValue('B' . $row, $storeCode);
                $sheet->setCellValue('C' . $row, $storeName);
                $sheet->setCellValue('D' . $row, \Carbon\Carbon::parse($batch->transaction_date)->format('m/d/y'));
                $sheet->setCellValue('E' . $row, '04'); // Default values
                $sheet->setCellValue('F' . $row, '10');
                $sheet->setCellValue('G' . $row, '72007');
                $sheet->setCellValue('H' . $row, $skuNumber);
                $sheet->setCellValue('I' . $row, $item->quantity);
                
                // Add borders to all cells
                for ($colIndex = 'A'; $colIndex <= 'I'; $colIndex++) {
                    $sheet->getStyle($colIndex . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                $totalQty += $item->quantity;
                $uniqueSkus->push($skuNumber);
                $row++;
            }
        }

        // Add summary rows
        $row += 2; // Empty row
        
        $sheet->setCellValue('A' . $row, 'TOTAL QTY: ' . $totalQty);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'TOTAL SKU/S: ' . $uniqueSkus->unique()->count());
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'RUN TIME: ' . now()->format('g:i:s a'));
        $row++;
        
        $sheet->setCellValue('A' . $row, 'RUN DATE: ' . now()->format('m/d/Y'));
        
        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}