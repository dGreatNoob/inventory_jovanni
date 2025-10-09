<?php

namespace App\Livewire\Pages\Supplies\Inventory;

use App\Models\SupplyProfile;
use App\Models\ItemType;
use App\Models\Allocation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $storageFilter = '';
    public $stockFilter = '';
    public $itemClassFilter = '';
    public $perPage = 10;

    // Options
    public $itemTypes;
    public $allocations;

    public function mount()
    {
        $this->itemTypes = ItemType::all();
        $this->allocations = Allocation::all();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStorageFilter()
    {
        $this->resetPage();
    }

    public function updatedStockFilter()
    {
        $this->resetPage();
    }

    public function updatedItemClassFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function edit($supplyId)
    {
        // TODO: Implement edit functionality
        session()->flash('info', 'Edit functionality coming soon!');
    }

    public function delete($supplyId)
    {
        try {
            $supply = SupplyProfile::findOrFail($supplyId);
            $supply->delete();
            
            session()->flash('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = SupplyProfile::with(['itemType', 'allocation']);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('supply_sku', 'like', '%' . $this->search . '%')
                  ->orWhere('supply_description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply item class filter
        if ($this->itemClassFilter) {
            $query->where('supply_item_class', $this->itemClassFilter);
        }

        // Apply stock level filter
        if ($this->stockFilter) {
            switch ($this->stockFilter) {
                case 'in_stock':
                    $query->where('supply_qty', '>', 0);
                    break;
                case 'low_stock':
                    $query->whereRaw('supply_qty <= (supply_qty * low_stock_threshold_percentage / 100)')
                          ->where('supply_qty', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('supply_qty', '<=', 0);
                    break;
            }
        }

        // Apply storage filter (placeholder - you can implement actual storage logic)
        if ($this->storageFilter) {
            // This would depend on your storage implementation
            // For now, we'll just apply a basic filter
        }

        $supplies = $query->orderBy('created_at', 'desc')
                         ->paginate($this->perPage);

        return view('livewire.pages.supplies.inventory.index', [
            'supplies' => $supplies,
        ]);
    }
}