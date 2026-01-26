<?php

namespace App\Http\Controllers;

use App\Models\BatchAllocation;
use App\Models\DeliveryReceipt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DRExcelController extends Controller
{
    public function exportDR(Request $request, $batchId, $branchId = null)
    {
        $batch = BatchAllocation::with([
            'branchAllocations.branch',
            'branchAllocations.items.product'
        ])->findOrFail($batchId);

        // If branchId is provided, filter to only that branch
        if ($branchId) {
            $batch->branchAllocations = $batch->branchAllocations->filter(function ($branchAllocation) use ($branchId) {
                return $branchAllocation->id == $branchId;
            });
        }

        try {
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set sheet title
            $sheet->setTitle('DR - ' . $batch->ref_no);

            // Add data to the spreadsheet
            $this->addDataToSheet($sheet, $batch);

            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            $fileName = 'DR_' . $batch->ref_no . '_' . now()->format('YmdHis') . '.xlsx';

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
            'DR No.', 'Store Code', 'Expected Delivery Date', 'Dept. Code', 'Sub-Dept Code', 'Class Code', 'SKU', 'Quantity'
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

            // Get the mother DR number for this branch allocation
            $motherDr = DeliveryReceipt::where('branch_allocation_id', $branchAllocation->id)
                ->where('type', 'mother')
                ->first();
            $drNumber = $motherDr ? $motherDr->dr_number : $batch->ref_no;

            // Only process original allocation items (without box_id)
            $originalItems = $branchAllocation->items()->whereNull('box_id')->get();

            // Process each original item
            foreach ($originalItems as $item) {
                $product = $item->product;
                $skuNumber = $product->sku ?? $product->id;

                $sheet->setCellValue('A' . $row, $drNumber);
                $sheet->setCellValue('B' . $row, $storeCode);
                $sheet->setCellValue('C' . $row, \Carbon\Carbon::parse($batch->created_at)->format('dmY')); // Expected Delivery Date in ddmmyy format
                $sheet->setCellValue('D' . $row, '41'); // Dept. Code - fixed
                $sheet->setCellValue('E' . $row, '72'); // Sub-Dept Code - fixed
                $sheet->setCellValue('F' . $row, '7'); // Class Code - fixed
                $sheet->setCellValue('G' . $row, $skuNumber);
                $sheet->setCellValue('H' . $row, $item->quantity);

                // Add borders to all cells
                for ($colIndex = 'A'; $colIndex <= 'H'; $colIndex++) {
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
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}