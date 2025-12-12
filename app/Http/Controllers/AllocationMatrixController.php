<?php

namespace App\Http\Controllers;

use App\Models\BatchAllocation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AllocationMatrixController extends Controller
{
    public function exportPDF(Request $request, $batchId)
    {
        $batch = BatchAllocation::with([
            'branchAllocations.branch',
            'branchAllocations.items.product'
        ])->findOrFail($batchId);

        try {
            // Get all products that have been allocated
            $selectedProductIds = $batch->branchAllocations
                ->flatMap(function ($branchAllocation) {
                    return $branchAllocation->items->pluck('product_id');
                })
                ->unique()
                ->values()
                ->toArray();

            $products = \App\Models\Product::whereIn('id', $selectedProductIds)->get()->keyBy('id');

            // Prepare data for the PDF
            $data = [
                'batch' => $batch,
                'products' => $products,
                'branchAllocations' => $batch->branchAllocations,
                'generated_at' => now(),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('pdf.allocation-matrix', $data);
            $pdf->setPaper('a4', 'portrait');

            $fileName = 'allocation_matrix_' . $batch->ref_no . '_' . now()->format('YmdHis') . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}