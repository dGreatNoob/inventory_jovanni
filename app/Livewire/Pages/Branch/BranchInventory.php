<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Product;
use App\Models\BranchAllocation;

class BranchInventory extends Component
{
    public $branches;
    public $products;
    public $branchProductStocks;
    public $branchProductBatches;
    public $showModal = false;
    public $selectedBranchId;
    public $selectedProductId;
    public $currentPage = 1;
    public $perPage = 5;

    public function mount()
    {
        $this->branches = Branch::all();
        $this->products = Product::all();

        // Load shipments indexed by branch_allocation_id
        $shipments = \App\Models\Shipment::all()->keyBy('branch_allocation_id');

        // Build branchProductStocks and branchProductBatches
        $this->branchProductStocks = [];
        $this->branchProductBatches = [];
        foreach ($this->branches as $branch) {
            foreach ($this->products as $product) {
                $this->branchProductBatches[$branch->id][$product->id] = [];
                $total = 0;

                // Find allocations for this branch and product
                $allocations = BranchAllocation::where('branch_id', $branch->id)
                    ->with(['items' => function($q) use($product) {
                        $q->where('product_id', $product->id);
                    }])
                    ->get();

                foreach ($allocations as $alloc) {
                    foreach ($alloc->items as $item) {
                        $shipment = $shipments[$alloc->id] ?? null;
                        if ($shipment) {
                            $this->branchProductBatches[$branch->id][$product->id][] = [
                                'reference' => $shipment->shipping_plan_num,
                                'date' => $shipment->scheduled_ship_date ? \Carbon\Carbon::parse($shipment->scheduled_ship_date)->format('Y-m-d') : $shipment->created_at->format('Y-m-d'),
                                'quantity' => $item->quantity
                            ];
                            $total += $item->quantity;
                        }
                    }
                }

                $this->branchProductStocks[$branch->id][$product->id] = $total;
            }
        }
    }

    public function openModal($branchId, $productId)
    {
        $this->selectedBranchId = $branchId;
        $this->selectedProductId = $productId;
        $this->currentPage = 1;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function nextPage()
    {
        $batches = collect($this->branchProductBatches[$this->selectedBranchId][$this->selectedProductId] ?? []);
        $totalPages = ceil($batches->count() / $this->perPage);
        if ($this->currentPage < $totalPages) {
            $this->currentPage++;
        }
    }

    public function prevPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function render()
    {
        return view('livewire.pages.branch.branch-inventory');
    }
}