<?php

namespace App\Livewire\SalesManagement;

use App\Models\SalesReturn as SalesReturnModel;
use App\Models\Branch;
use App\Models\Shipment;
use App\Models\BranchAllocationItem;
use Livewire\Component;
use Livewire\WithPagination;

class SalesReturn extends Component
{
    use WithPagination;

    public $search = '';
    public $showCreateModal = false;
    public $showViewModal = false;
    public $selectedSalesReturn;

    // Create modal properties
    public $selectedBranch = '';
    public $selectedShipment = '';
    public $returnReason = '';
    public $selectedItems = [];

    protected $rules = [
        'selectedBranch' => 'required',
        'selectedShipment' => 'required',
        'returnReason' => 'required|string|max:255',
        'selectedItems' => 'required|array|min:1',
        'selectedItems.*.branch_allocation_item_id' => 'required',
        'selectedItems.*.quantity' => 'required|integer|min:1',
        'selectedItems.*.reason' => 'nullable|string|max:255',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['selectedBranch', 'selectedShipment', 'returnReason', 'selectedItems']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['selectedBranch', 'selectedShipment', 'returnReason', 'selectedItems']);
    }

    public function updatedSelectedBranch()
    {
        $this->selectedShipment = '';
        $this->selectedItems = [];
    }

    public function updatedSelectedShipment()
    {
        $this->loadAvailableItems();
    }

    public function loadAvailableItems()
    {
        if ($this->selectedShipment) {
            $shipment = Shipment::with(['branchAllocation.items'])->find($this->selectedShipment);
            if ($shipment && $shipment->shipping_status === 'completed') {
                $this->selectedItems = $shipment->branchAllocation->items->map(function ($item) {
                    return [
                        'branch_allocation_item_id' => $item->id,
                        'product_name' => $item->display_name,
                        'available_quantity' => $item->quantity,
                        'quantity' => 0,
                        'reason' => '',
                    ];
                })->toArray();
            }
        }
    }

    public function createSalesReturn()
    {
        $this->validate();

        $salesReturn = SalesReturnModel::create([
            'branch_id' => $this->selectedBranch,
            'shipment_id' => $this->selectedShipment,
            'status' => 'pending',
            'reason' => $this->returnReason,
            'created_by' => auth()->user()->id,
        ]);

        foreach ($this->selectedItems as $item) {
            if ($item['quantity'] > 0) {
                $salesReturn->items()->create([
                    'branch_allocation_item_id' => $item['branch_allocation_item_id'],
                    'quantity' => $item['quantity'],
                    'reason' => $item['reason'],
                ]);
            }
        }

        session()->flash('message', 'Sales return created successfully.');
        $this->closeCreateModal();
        $this->resetPage();
    }

    public function viewSalesReturn($id)
    {
        $this->selectedSalesReturn = SalesReturnModel::with(['branch', 'shipment', 'items.branchAllocationItem'])->find($id);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedSalesReturn = null;
    }

    public function completeSalesReturn()
    {
        if ($this->selectedSalesReturn) {
            $this->selectedSalesReturn->update([
                'status' => 'completed',
                'completed_by' => auth()->user()->id,
                'completed_at' => now(),
            ]);

            session()->flash('message', 'Sales return completed successfully.');
            $this->closeViewModal();
        }
    }

    public function render()
    {
        $salesReturns = SalesReturnModel::with(['branch', 'shipment'])
            ->when($this->search, function ($query) {
                $query->where('sales_return_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('branch', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $branches = Branch::all();
        $shipments = collect();

        if ($this->selectedBranch) {
            $shipments = Shipment::whereHas('branchAllocation', function ($query) {
                $query->where('branch_id', $this->selectedBranch);
            })
            ->where('shipping_status', 'completed')
            ->with('branchAllocation')
            ->get();
        }

        return view('livewire.pages.sales-management.sales-return', [
            'salesReturns' => $salesReturns,
            'branches' => $branches,
            'shipments' => $shipments,
        ]);
    }
}
