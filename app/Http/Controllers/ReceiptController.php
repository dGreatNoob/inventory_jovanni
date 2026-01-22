<?php

namespace App\Http\Controllers;

use App\Models\SalesReceipt;
use App\Services\ReceiptExporter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReceiptController extends Controller
{
    protected $exporter;

    public function __construct(ReceiptExporter $exporter)
    {
        $this->exporter = $exporter;
    }

    public function exportPDF($receiptId)
    {
        try {
            $receipt = SalesReceipt::find($receiptId);
            
            if (!$receipt) {
                return response()->json(['error' => 'Receipt not found'], 404);
            }

            // Use the existing ReceiptExporter service
            $pdfResponse = $this->exporter->exportToPDF($receiptId);
            
            return $pdfResponse;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    public function exportExcel($receiptId)
    {
        try {
            $receipt = SalesReceipt::find($receiptId);
            
            if (!$receipt) {
                return response()->json(['error' => 'Receipt not found'], 404);
            }

            // Use the existing ReceiptExporter service
            $excelResponse = $this->exporter->exportToExcel($receiptId);
            
            return $excelResponse;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate Excel: ' . $e->getMessage()], 500);
        }
    }
}