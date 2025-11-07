<?php

namespace App\Livewire\Pages\POManagement\PurchaseOrder;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Analytics extends Component
{
    public $dateFrom;
    public $dateTo;
    public $selectedSupplier = '';
    public $reportType = 'summary';

    public function mount()
    {
        // Default to current month
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    private function getPurchaseOrderSummary()
    {
        $query = PurchaseOrder::query()
            ->whereBetween('order_date', [$this->dateFrom, $this->dateTo]);

        if ($this->selectedSupplier) {
            $query->where('supplier_id', $this->selectedSupplier);
        }

        return [
            'total_pos' => $query->count(),
            'total_amount' => $query->sum('total_price') ?? 0,
            'total_qty' => $query->sum('total_qty') ?? 0,
            'by_status' => $query->select('status', DB::raw('count(*) as count'), DB::raw('sum(total_price) as total'))
                ->groupBy('status')
                ->get(),
        ];
    }

    private function getOutstandingPOs()
    {
        $query = PurchaseOrder::query()
            ->whereIn('status', ['pending', 'approved', 'for_delivery', 'delivered'])
            ->with(['supplier', 'department'])
            ->orderBy('order_date', 'desc');

        if ($this->selectedSupplier) {
            $query->where('supplier_id', $this->selectedSupplier);
        }

        return $query->get();
    }

    private function getSupplierPurchaseHistory()
    {
        $query = PurchaseOrder::query()
            ->whereBetween('order_date', [$this->dateFrom, $this->dateTo])
            ->with('supplier')
            ->select('supplier_id', 
                DB::raw('count(*) as total_orders'),
                DB::raw('sum(total_price) as total_spent'),
                DB::raw('sum(total_qty) as total_items'))
            ->groupBy('supplier_id')
            ->orderBy('total_spent', 'desc');

        if ($this->selectedSupplier) {
            $query->where('supplier_id', $this->selectedSupplier);
        }

        return $query->get();
    }

    private function getLeadTimeAnalysis()
    {
        // Build query with filters
        $query = PurchaseOrder::query()
            ->whereBetween('order_date', [$this->dateFrom, $this->dateTo]);

        // Apply supplier filter if selected
        if ($this->selectedSupplier) {
            $query->where('supplier_id', $this->selectedSupplier);
        }

        // Get POs with supplier relationship
        $pos = $query->with('supplier')->get();

        // Map to lead time analysis data
        return $pos->map(function ($po) {
            $expectedLeadTime = null;
            $actualLeadTime = null;
            $difference = null;

            // Calculate Expected Lead Time (order date to expected delivery date)
            if ($po->order_date && $po->expected_delivery_date) {
                $orderDate = Carbon::parse($po->order_date)->startOfDay();
                $expectedDate = Carbon::parse($po->expected_delivery_date)->startOfDay();
                $expectedLeadTime = $orderDate->diffInDays($expectedDate);
            }

            // Calculate Actual Lead Time (order date to actual delivery date)
            if ($po->order_date && $po->del_on) {
                $orderDate = Carbon::parse($po->order_date)->startOfDay();
                $deliveryDate = Carbon::parse($po->del_on)->startOfDay();
                $actualLeadTime = $orderDate->diffInDays($deliveryDate);
            }

            // Calculate Variance (Actual - Expected)
            if ($expectedLeadTime !== null && $actualLeadTime !== null) {
                $difference = $actualLeadTime - $expectedLeadTime;
            }

            return [
                'po_num' => $po->po_num,
                'supplier' => $po->supplier->name ?? 'N/A',
                'order_date' => $po->order_date,
                'expected_delivery' => $po->expected_delivery_date,
                'actual_delivery' => $po->del_on,
                'expected_lead_time' => $expectedLeadTime,
                'actual_lead_time' => $actualLeadTime,
                'difference' => $difference,
            ];
        });
    }

    private function getData()
    {
        $data = [];
        
        switch ($this->reportType) {
            case 'summary':
                $data['summary'] = $this->getPurchaseOrderSummary();
                break;
            case 'outstanding':
                $data['outstanding'] = $this->getOutstandingPOs();
                break;
            case 'supplier_history':
                $data['supplier_history'] = $this->getSupplierPurchaseHistory();
                break;
            case 'lead_time':
                $data['lead_time'] = $this->getLeadTimeAnalysis();
                break;
        }

        return $data;
    }

    public function exportReport()
    {
        // TODO: Implement export functionality (CSV/PDF)
        session()->flash('message', 'Export functionality coming soon!');
    }

    public function render()
    {
        // ✅ Fix 1: Remove 'active' filter to show all suppliers
        $suppliers = Supplier::orderBy('name')->get();
        
        // ✅ Fix 2: Add debug to check if suppliers exist
        // \Log::info('Suppliers count: ' . $suppliers->count());
        
        $data = $this->getData();

        return view('livewire.pages.POmanagement.purchase-order.analytics', [
            'suppliers' => $suppliers,
            'data' => $data,
        ]);
    }
}