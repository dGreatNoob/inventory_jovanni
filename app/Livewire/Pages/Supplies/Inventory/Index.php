<?php

namespace App\Livewire\Pages\Supplies\Inventory;

use App\Models\SupplyProfile;
use App\Models\ItemType;
use App\Models\Allocation;
use App\Models\SupplyBatch;
use App\Models\SupplyOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $search = '';

    // Filter properties
    public $itemClassFilter = 'consumable';
    public $itemTypeFilter = '';
    public $allocationFilter = '';

    // Form properties
    #[Validate('required|string|max:255')]
    public $supply_item_class = '';

    #[Validate('required|exists:item_types,id')]
    public $item_type_id = '';

    #[Validate('required|exists:allocations,id')]
    public $allocation_id = '';

    #[Validate('required|string|max:255')]
    public $supply_description = '';

    #[Validate('required|string|max:255')]
    public $supply_uom = '';

    // Current quantity is always 0 for new product profiles
    public $supply_qty = 0;

    #[Validate('required|numeric|min:0')]
    public $low_stock_threshold_percentage = 20;

    #[Validate('required|numeric|min:0')]
    public $unit_cost = '';

    #[Validate('required|numeric|min:0')]
    public $supply_price1 = '';
    #[Validate('required|numeric|min:0')]
    public $supply_price2 = '';
    #[Validate('required|numeric|min:0')]
    public $supply_price3 = '';
    #[Validate('required|string|max:255')]
    public $supply_sku = '';

    public int $perPage = 10;

    // Edit property
    public $editingSupplyId = null;

    // Delete property
    public $deletingSupplyId = null;

    // QR Code Modal properties
    public $showQrModal = false;
    public $selectedSupply = null;

    // Modal states
    public $showEditModal = false;
    public $showDeleteModal = false;

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

    public function updatedItemClassFilter()
    {
        $this->resetPage();
    }

    public function updatedItemTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedAllocationFilter()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->validate();

        SupplyProfile::create([
            'supply_sku' => $this->supply_sku,
            'supply_item_class' => $this->supply_item_class,
            'item_type_id' => $this->item_type_id,
            'allocation_id' => $this->allocation_id,
            'supply_description' => $this->supply_description,
            'supply_qty' => $this->supply_qty,
            'supply_uom' => $this->supply_uom,
            'low_stock_threshold_percentage' => $this->low_stock_threshold_percentage,
            'unit_cost' => $this->unit_cost,
            'supply_price1' => $this->supply_price1,
            'supply_price2' => $this->supply_price2,
            'supply_price3' => $this->supply_price3,
        ]);

        session()->flash('message', 'Product profile created successfully!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $supply = SupplyProfile::findOrFail($id);
        $this->editingSupplyId = $id;
        $this->supply_item_class = $supply->supply_item_class;
        $this->item_type_id = $supply->item_type_id;
        $this->allocation_id = $supply->allocation_id;
        $this->supply_description = $supply->supply_description;
        $this->supply_qty = $supply->supply_qty;
        $this->supply_uom = $supply->supply_uom;
        $this->low_stock_threshold_percentage = $supply->low_stock_threshold_percentage;
        $this->unit_cost = $supply->unit_cost;
        $this->supply_price1 = $supply->supply_price1;
        $this->supply_price2 = $supply->supply_price2;
        $this->supply_price3 = $supply->supply_price3;
        $this->supply_sku = $supply->supply_sku;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate();

        $supply = SupplyProfile::findOrFail($this->editingSupplyId);
        $supply->update([
            'supply_item_class' => $this->supply_item_class,
            'item_type_id' => $this->item_type_id,
            'allocation_id' => $this->allocation_id,
            'supply_description' => $this->supply_description,
            'supply_qty' => $this->supply_qty,
            'supply_uom' => $this->supply_uom,
            'low_stock_threshold_percentage' => $this->low_stock_threshold_percentage,
            'unit_cost' => $this->unit_cost,
            'supply_price1' => $this->supply_price1,
            'supply_price2' => $this->supply_price2,
            'supply_price3' => $this->supply_price3,
            'supply_sku' => $this->supply_sku
        ]);

        $this->resetForm();
        session()->flash('message', 'Supply profile updated successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deletingSupplyId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $supply = SupplyProfile::findOrFail($this->deletingSupplyId);
        $supply->delete();
        
        $this->reset(['deletingSupplyId', 'showDeleteModal']);
        session()->flash('message', 'Supply profile deleted successfully.');
    }

    public function showQrCode($id)
    {
        $this->selectedSupply = SupplyProfile::with(['itemType', 'allocation'])->findOrFail($id);
        $this->showQrModal = true;
    }

    public function closeQrModal()
    {
        $this->showQrModal = false;
        $this->selectedSupply = null;
    }

    public function cancel()
    {
        $this->resetForm();
        $this->reset(['showEditModal', 'showDeleteModal', 'showQrModal', 'editingSupplyId', 'deletingSupplyId', 'selectedSupply']);
    }

    protected function resetForm()
    {
        $this->reset([
            'supply_sku',
            'supply_item_class',
            'item_type_id',
            'allocation_id',
            'supply_description',
            'supply_qty',
            'supply_uom',
            'low_stock_threshold_percentage',
            'unit_cost',
            'supply_price1',
            'supply_price2',
            'supply_price3',
        ]);
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function getDashboardStatsProperty()
    {
        $totalProducts = SupplyProfile::count();
        $consumables = SupplyProfile::where('supply_item_class', 'consumable')->count();
        $accessories = SupplyProfile::where('supply_item_class', 'accessories')->count();
        $lowStockItems = SupplyProfile::whereRaw('supply_qty <= (supply_min_qty * (low_stock_threshold_percentage / 100))')
                                     ->count();
        $totalValue = SupplyProfile::selectRaw('SUM(supply_qty * unit_cost) as total_value')
                                  ->first()->total_value ?? 0;

        return [
            [
                'label' => 'Total Products',
                'value' => number_format($totalProducts),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
                'gradient' => 'from-blue-500 to-blue-600'
            ],
            [
                'label' => 'Consumables',
                'value' => number_format($consumables),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012 2v2M7 7h10"></path></svg>',
                'gradient' => 'from-green-500 to-green-600'
            ],
            [
                'label' => 'Low Stock Items',
                'value' => number_format($lowStockItems),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
                'gradient' => 'from-red-500 to-red-600'
            ],
            [
                'label' => 'Total Value',
                'value' => '₱' . number_format($totalValue, 2),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>',
                'gradient' => 'from-purple-500 to-purple-600'
            ]
        ];
    }

    public function getRecentStockInProperty()
    {
        return SupplyBatch::with(['supplyProfile', 'supplyOrder.purchaseOrder'])
                          ->where('received_date', '>=', now()->subDays(7))
                          ->orderBy('received_date', 'desc')
                          ->limit(10)
                          ->get();
    }

    public function getRecentStockOutProperty()
    {
        // Get batches that had quantity reductions in the last 7 days
        // Since we don't have a movement table, we'll simulate with batches that have low current_qty relative to initial_qty
        return SupplyBatch::with(['supplyProfile'])
                          ->whereRaw('current_qty < initial_qty')
                          ->where('updated_at', '>=', now()->subDays(7))
                          ->orderBy('updated_at', 'desc')
                          ->limit(10)
                          ->get();
    }

    public function render()
    {
        $supplies = SupplyProfile::query()
            ->with(['itemType', 'allocation'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('supply_description', 'like', '%' . $this->search . '%')
                        ->orWhere('supply_sku', 'like', '%' . $this->search . '%')
                        ->orWhereHas('itemType', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('allocation', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->itemClassFilter, function ($query) {
                $query->where('supply_item_class', $this->itemClassFilter);
            })
            ->when($this->itemTypeFilter, function ($query) {
                $query->where('item_type_id', $this->itemTypeFilter);
            })
            ->when($this->allocationFilter, function ($query) {
                $query->where('allocation_id', $this->allocationFilter);
            })
            ->latest()
            ->paginate($this->perPage);

        // Load batch information for consumable items
        $supplies->each(function ($supply) {
            if ($supply->isConsumable()) {
                $supply->load(['activeBatches' => function ($query) {
                    $query->orderBy('expiration_date', 'asc')->limit(5);
                }]);
                
                // Calculate batch statistics
                $supply->total_batch_qty = $supply->getTotalBatchQuantity();
                $supply->expired_batches_count = $supply->supplyBatches()->expired()->count();
                $supply->expiring_soon_count = $supply->supplyBatches()->expiringSoon(30)->count();
                $supply->next_expiry = $supply->activeBatches()
                    ->whereNotNull('expiration_date')
                    ->orderBy('expiration_date', 'asc')
                    ->first()?->expiration_date;
            }
        });

        return view('livewire.pages.supplies.inventory.index', [
            'supplies' => $supplies,
            'itemTypes' => $this->itemTypes,
            'allocations' => $this->allocations,
            'uomOptions' => SupplyProfile::getUnitOfMeasureOptions(),
            'dashboardStats' => $this->getDashboardStatsProperty(),
            'recentStockIn' => $this->getRecentStockInProperty(),
            'recentStockOut' => $this->getRecentStockOutProperty()
        ]);
    }
}
