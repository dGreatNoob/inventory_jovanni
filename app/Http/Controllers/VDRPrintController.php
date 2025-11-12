<?php

namespace App\Http\Controllers;

use App\Models\BatchAllocation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VDRPrintController extends Controller
{
    public function printVDR(Request $request, $batchId)
    {
        $batch = BatchAllocation::with([
            'branchAllocations.branch',
            'branchAllocations.items.product'
        ])->findOrFail($batchId);

        $preparedBy = $request->query('prepared_by');
        
        $html = $this->generateVDRHTML($batch, $preparedBy);
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="VDR_' . $batch->ref_no . '.html"');
    }

    private function generateVDRHTML($batch)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>VDR - ' . $batch->ref_no . '</title>
            <style>
                body {
                    font-family: "Courier New", monospace;
                    font-size: 12px;
                    margin: 20px;
                    line-height: 1.2;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    border: 2px solid #000;
                    padding: 10px;
                }
                .vendor-info {
                    margin: 10px 0;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 10px 0;
                }
                th, td {
                    border: 1px solid #000;
                    padding: 4px;
                    text-align: left;
                }
                th {
                    background-color: #f0f0f0;
                    font-weight: bold;
                }
                .totals {
                    margin-top: 20px;
                    border: 2px solid #000;
                    padding: 10px;
                }
                .total-row {
                    display: flex;
                    justify-content: space-between;
                    margin: 5px 0;
                }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                }
                .page-info {
                    text-align: right;
                    margin: 10px 0;
                }
                .print-button {
                    text-align: center;
                    margin: 20px 0;
                    position: sticky;
                    top: 0;
                    background: white;
                    padding: 10px;
                    z-index: 1000;
                    border: 2px solid #007bff;
                    border-radius: 8px;
                }
                .print-button button {
                    padding: 10px 20px;
                    background: #007bff;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: bold;
                    margin: 0 5px;
                    transition: background-color 0.3s;
                }
                .print-button button:hover {
                    background: #0056b3;
                }
                .print-button button.close-btn {
                    background: #6c757d;
                }
                .print-button button.close-btn:hover {
                    background: #545b62;
                }
                
                /* Hide print button during actual printing */
                @media print {
                    .print-button {
                        display: none !important;
                    }
                    
                    /* Remove page margins for printing */
                    body {
                        margin: 0;
                        padding: 10px;
                    }
                    
                    /* Ensure tables break properly */
                    table {
                        page-break-inside: auto;
                    }
                    tr {
                        page-break-inside: avoid;
                        page-break-after: auto;
                    }
                }
            </style>
        </head>
        <body>
            <div class="page-info">PAGE: 1 of 1</div>
            
            <!-- Print Button -->
            <div class="print-button">
                <button onclick="window.print()" title="Print this VDR document">
                    🖨️ Print VDR
                </button>
                <button onclick="window.close()" class="close-btn" title="Close this window">
                    ✕ Close
                </button>
                <div style="font-size: 11px; color: #666; margin-top: 5px;">
                    Click "Print VDR" to open printer options
                </div>
            </div>
            
            <!-- Vendor Information Header -->
            <div class="vendor-header" style="text-align: center; margin: 20px 0; padding: 15px; border: 2px solid #000; background-color: #f8f9fa;">
                <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">STORE CONSIGNOR</div>
                <div style="font-weight: bold; font-size: 14px; margin-bottom: 8px;">VALIDATED DELIVERY RECEIPT</div>
                <div style="font-size: 12px; line-height: 1.4;">
                    <strong>VENDOR CODE:</strong> 104148<br>
                    <strong>VENDOR NAME:</strong> JKF CORP.
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>DR#</th>
                        <th>STORE CODE</th>
                        <th>STORE NAME</th>
                        <th>EXP. DELIVERY DATE (MMDDYY)</th>
                        <th>DP</th>
                        <th>SD</th>
                        <th>CL</th>
                        <th>CLASS DESC</th>
                        <th>BOXES</th>
                        <th>SKU #</th>
                        <th>SKU DESC</th>
                        <th>QTY</th>
                    </tr>
                </thead>
                <tbody>';

        $totalQty = 0;
        $totalBoxes = 0;
        $uniqueSkus = collect();

        foreach ($batch->branchAllocations as $branchAllocation) {
            $branch = $branchAllocation->branch;
            $storeCode = $branch->code ?? '';
            $storeName = $branch->name ?? '';

            foreach ($branchAllocation->items as $item) {
                $product = $item->product;
                $skuNumber = $product->sku ?? $product->id;
                $skuDescription = $product->name ?? '';
                
                $html .= '<tr>';
                $html .= '<td>' . $batch->ref_no . '</td>';
                $html .= '<td>' . $storeCode . '</td>';
                $html .= '<td>' . $storeName . '</td>';
                $html .= '<td>' . \Carbon\Carbon::parse($batch->transaction_date)->format('m/d/y') . '</td>';
                $html .= '<td>04</td>';
                $html .= '<td>10</td>';
                $html .= '<td>72007</td>';
                $html .= '<td>JOVANNI</td>';
                $html .= '<td>0</td>'; // Will be implemented later
                $html .= '<td>' . $skuNumber . '</td>';
                $html .= '<td>' . $skuDescription . '</td>';
                $html .= '<td>' . $item->quantity . '</td>';
                $html .= '</tr>';

                $totalQty += $item->quantity;
                $totalBoxes += 0; // To be implemented later
                $uniqueSkus->push($skuNumber);
            }
        }

        $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                <div class="totals">
                    <div class="total-row">
                        <span><strong>TOTAL QTY: ' . $totalQty . '</strong></span>
                        <span><strong>TOTAL BOXES: ' . $totalBoxes . '</strong></span>
                    </div>
                    <div class="total-row">
                        <span><strong>TOTAL SKU/S: ' . $uniqueSkus->unique()->count() . '</strong></span>
                        <span><strong>RUN TIME: ' . now()->format('g:i:s a') . '</strong></span>
                    </div>
                    <div class="total-row">
                        <span><strong>RUN DATE: ' . now()->format('m/d/Y') . '</strong></span>
                        <span>&nbsp;</span>
                    </div>
                    <div class="total-row" style="margin-top: 20px; text-align: left;">
                        <span><strong>Prepared By: ' . ($preparedBy ?? '_______________________________') . '</strong></span>
                        <span>&nbsp;</span>
                    </div>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }
}