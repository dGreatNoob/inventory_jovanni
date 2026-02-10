<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Product;
use App\Models\SalesOrderBranchItem;
use App\Models\Shipment;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesTracker extends Component
{
    public $branches;
    public $products;
    public $branchProductSales;
    public $startDate;
    public $endDate;

    // Modal properties
    public $showAddSalesModal = false;
    public $selectedShipmentId = null;
    public $completedShipments = [];
    public $shipmentProducts = [];
    public $barcodeInput = '';
    public $scanFeedback = '';
    public $scannedQuantities = []; // branch_id => product_id => scanned_qty

    public function mount()
    {
        $this->branches = Branch::with('products')->get();
        $this->products = Product::active()->get();

        // Default to last 30 days
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');

        $this->loadSalesData();
    }

    public function updatedStartDate()
    {
        $this->loadSalesData();
    }

    public function updatedEndDate()
    {
        $this->loadSalesData();
    }

    private function loadSalesData()
    {
        $this->branchProductSales = [];

        // Aggregate sales data from SalesOrderBranchItem
        $salesData = SalesOrderBranchItem::whereHas('salesOrder', function($query) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate . ' 23:59:59']);
        })
        ->selectRaw('branch_id, product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
        ->groupBy('branch_id', 'product_id')
        ->get();

        // Build the mapping
        foreach ($this->branches as $branch) {
            foreach ($this->products as $product) {
                $sale = $salesData->where('branch_id', $branch->id)->where('product_id', $product->id)->first();
                $this->branchProductSales[$branch->id][$product->id] = [
                    'quantity' => $sale ? $sale->total_quantity : 0,
                    'revenue' => $sale ? $sale->total_revenue : 0,
                ];
            }
        }
    }

    // Modal methods
    public function openAddSalesModal()
    {
        $this->resetModal();
        $this->loadCompletedShipments();
        $this->dispatch('modal-opened', name: 'add-sales');
    }

    public function closeAddSalesModal()
    {
        $this->showAddSalesModal = false;
        $this->resetModal();
        $this->loadSalesData(); // Reset to database values
    }

    private function resetModal()
    {
        $this->selectedShipmentId = null;
        $this->shipmentProducts = [];
        $this->barcodeInput = '';
        $this->scanFeedback = '';
        $this->scannedQuantities = [];
    }

    private function loadCompletedShipments()
    {
        $this->completedShipments = Shipment::with('batchAllocation')
            ->where('shipping_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updatedSelectedShipmentId()
    {
        if ($this->selectedShipmentId) {
            $this->loadShipmentProducts();
        } else {
            $this->shipmentProducts = [];
        }
    }

    private function loadShipmentProducts()
    {
        if (!$this->selectedShipmentId) {
            return;
        }

        $shipment = Shipment::with('branchAllocation.items.product')->find($this->selectedShipmentId);

        if (!$shipment || !$shipment->branchAllocation) {
            $this->shipmentProducts = [];
            return;
        }

        $this->shipmentProducts = [];
        foreach ($shipment->branchAllocation->items as $item) {
            $this->shipmentProducts[] = [
                'id' => $item->product_id,
                'name' => $item->product->name,
                'barcode' => $item->product->barcode,
                'allocated_qty' => $item->quantity,
                'scanned_qty' => $this->scannedQuantities[$shipment->branchAllocation->branch_id][$item->product_id] ?? 0,
                'branch_id' => $shipment->branchAllocation->branch_id,
            ];
        }
    }

    public function processBarcode()
    {
        if (empty($this->barcodeInput)) {
            return;
        }

        $barcode = trim($this->barcodeInput);

        // Find product by barcode
        $product = Product::active()->where('barcode', $barcode)->first();

        if (!$product) {
            $this->scanFeedback = "❌ Barcode '{$barcode}' not found in system!";
            $this->barcodeInput = '';
            return;
        }

        $unitPrice = $product->price ?? $product->selling_price ?? 0;

        // Check if product is in the selected shipment
        $shipment = Shipment::with('branchAllocation.items')->find($this->selectedShipmentId);
        $branchAllocationItem = $shipment->branchAllocation->items->where('product_id', $product->id)->first();

        if (!$branchAllocationItem) {
            $this->scanFeedback = "❌ Product '{$product->name}' is not in the selected shipment!";
            $this->barcodeInput = '';
            return;
        }

        // Get current scanned quantity
        $branchId = $shipment->branchAllocation->branch_id;
        $productId = $product->id;
        $currentScanned = $this->scannedQuantities[$branchId][$productId] ?? 0;
        $allocatedQty = $branchAllocationItem->quantity;

        if ($currentScanned >= $allocatedQty) {
            $this->scanFeedback = "⚠️ Product '{$product->name}' already fully scanned!";
        } else {
            // Increment scanned quantity
            $this->scannedQuantities[$branchId][$productId] = $currentScanned + 1;
            $newScanned = $this->scannedQuantities[$branchId][$productId];

            // Update real-time sales display
            if (!isset($this->branchProductSales[$branchId][$productId])) {
                $this->branchProductSales[$branchId][$productId] = ['quantity' => 0, 'revenue' => 0];
            }
            $this->branchProductSales[$branchId][$productId]['quantity'] += 1;
            $this->branchProductSales[$branchId][$productId]['revenue'] += $unitPrice;

            if ($newScanned >= $allocatedQty) {
                $this->scanFeedback = "✅ {$product->name} - SCAN COMPLETE!";
            } else {
                $remaining = $allocatedQty - $newScanned;
                $this->scanFeedback = "✅ {$product->name} - {$newScanned}/{$allocatedQty} scanned ({$remaining} remaining)";
            }

            // Update shipment products display
            $this->loadShipmentProducts();

            // Auto-save and close modal when all products scanned
            if ($this->allProductsScanned) {
                $this->saveSalesData();
            }
        }

        $this->barcodeInput = '';
    }

    public function changeShipment()
    {
        $this->selectedShipmentId = null;
        $this->shipmentProducts = [];
        $this->scannedQuantities = [];
        $this->scanFeedback = '';
        $this->barcodeInput = '';
    }

    public function getAllProductsScannedProperty()
    {
        if (!$this->selectedShipmentId || empty($this->shipmentProducts)) {
            return false;
        }

        foreach ($this->shipmentProducts as $product) {
            $scanned = $this->scannedQuantities[$product['branch_id']][$product['id']] ?? 0;
            if ($scanned < $product['allocated_qty']) {
                return false;
            }
        }

        return true;
    }

    public function saveSalesData()
    {
        // Simple debug - this should always appear in logs
        Log::info('SAVE SALES DATA METHOD CALLED', [
            'selectedShipmentId' => $this->selectedShipmentId,
            'timestamp' => now()->toISOString()
        ]);

        // Debug logging
        Log::info('=== SAVE SALES DATA STARTED ===', [
            'selectedShipmentId' => $this->selectedShipmentId,
            'allProductsScanned' => $this->allProductsScanned,
            'scannedQuantities' => $this->scannedQuantities,
            'shipmentProducts' => $this->shipmentProducts,
            'timestamp' => now()
        ]);

        // Add session flash for immediate feedback
        session()->flash('debug_info', 'Save method called with shipment: ' . $this->selectedShipmentId);

        // Force a response to ensure Livewire processes this
        $this->dispatch('debug-message', message: 'Save method triggered');

        // Validation
        $this->validate([
            'selectedShipmentId' => 'required|exists:shipments,id',
        ], [
            'selectedShipmentId.required' => 'Please select a shipment.',
            'selectedShipmentId.exists' => 'Selected shipment does not exist.',
        ]);

        if (!$this->allProductsScanned) {
            session()->flash('error', 'Please complete scanning all products before saving.');
            Log::warning('Save blocked: not all products scanned');
            return;
        }

        if (empty($this->scannedQuantities)) {
            session()->flash('error', 'No products have been scanned.');
            Log::warning('Save blocked: no scanned quantities');
            return;
        }

        $shipment = Shipment::with('branchAllocation')->find($this->selectedShipmentId);

        if (!$shipment) {
            session()->flash('error', 'Shipment not found.');
            Log::error('Shipment not found', ['shipment_id' => $this->selectedShipmentId]);
            return;
        }

        if (!$shipment->branchAllocation) {
            session()->flash('error', 'Shipment has no branch allocation.');
            Log::error('Shipment has no branch allocation', ['shipment_id' => $this->selectedShipmentId]);
            return;
        }

        Log::info('Starting database transaction', [
            'shipment_id' => $shipment->id,
            'branch_allocation_id' => $shipment->branchAllocation->id,
            'branch_id' => $shipment->branchAllocation->branch_id
        ]);

        DB::beginTransaction();
        try {
            // Create a sales order for this shipment sales
            $salesOrder = \App\Models\SalesOrder::create([
                'status' => 'completed',
                'customer_id' => $shipment->branchAllocation->branch_id, // The branch that received the shipment
                'contact_person_name' => $shipment->customer_name,
                'phone' => $shipment->customer_phone,
                'email' => $shipment->customer_email,
                'shipping_address' => $shipment->customer_address,
                'payment_method' => 'cash', // Default
                'shipping_method' => 'delivered', // Since shipment is completed
                'delivery_date' => now(),
            ]);

            Log::info('Sales order created', ['sales_order_id' => $salesOrder->id]);

            // Create sales order branch items
            $totalItems = 0;
            foreach ($this->scannedQuantities as $branchId => $products) {
                foreach ($products as $productId => $scannedQty) {
                    if ($scannedQty > 0) {
                        $product = Product::find($productId);
                        if (!$product) {
                            Log::warning('Product not found', ['product_id' => $productId]);
                            continue;
                        }

                        $unitPrice = $product->price ?? $product->selling_price ?? 0;

                        SalesOrderBranchItem::create([
                            'sales_order_id' => $salesOrder->id,
                            'branch_id' => $branchId,
                            'product_id' => $productId,
                            'unit_price' => $unitPrice,
                            'quantity' => $scannedQty,
                            'subtotal' => $scannedQty * $unitPrice,
                        ]);

                        Log::info('Sales order item created', [
                            'sales_order_id' => $salesOrder->id,
                            'branch_id' => $branchId,
                            'product_id' => $productId,
                            'quantity' => $scannedQty
                        ]);

                        $totalItems++;
                    }
                }
            }

            DB::commit();

            Log::info('=== SAVE SALES DATA COMPLETED ===', [
                'sales_order_id' => $salesOrder->id,
                'total_items' => $totalItems
            ]);

            Log::info('=== SAVE SALES DATA SUCCESS ===', [
                'sales_order_id' => $salesOrder->id,
                'total_items' => $totalItems
            ]);

            session()->flash('message', "✅ Sales data saved successfully! Created sales order #{$salesOrder->id} with {$totalItems} items.");

            // Close modal and reset
            $this->showAddSalesModal = false;
            $this->resetModal();
            $this->loadSalesData(); // Refresh the sales tracker

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('=== SAVE SALES DATA FAILED ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'shipment_id' => $this->selectedShipmentId
            ]);
            session()->flash('error', '❌ Failed to save sales data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.branch.sales-tracker');
    }
}