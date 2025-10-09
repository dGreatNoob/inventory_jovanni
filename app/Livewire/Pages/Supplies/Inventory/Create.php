<?php

namespace App\Livewire\Pages\Supplies\Inventory;

use App\Models\Allocation;
use App\Models\ItemType;
use App\Models\SupplyProfile;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Create extends Component
{
    // Form fields
    public $supply_sku = '';
    public $supply_item_class = '';
    public $item_type_id = '';
    public $allocation_id = '';
    public $supply_uom = '';
    public $supply_description = '';
    public $supply_qty = 0;
    public $low_stock_threshold_percentage = 20;
    public $unit_price = 0;
    public $unit_cost = 0;

    // Options
    public $itemTypes;
    public $allocations;
    public $uomOptions = [
        'pcs' => 'Pieces',
        'boxes' => 'Boxes',
        'kg' => 'Kilograms',
        'grams' => 'Grams',
        'liters' => 'Liters',
        'ml' => 'Milliliters',
        'dimensions' => 'Dimensions'
    ];

    public function mount()
    {
        $this->itemTypes = ItemType::all();
        $this->allocations = Allocation::all();
    }

    public function create()
    {
        $this->validate([
            'supply_sku' => 'required|string|max:255|unique:supply_profiles,supply_sku',
            'supply_item_class' => 'required|string|in:consumable,accessories',
            'item_type_id' => 'required|exists:item_types,id',
            'allocation_id' => 'required|exists:allocations,id',
            'supply_uom' => 'required|string',
            'supply_description' => 'nullable|string',
            'supply_qty' => 'required|numeric|min:0',
            'low_stock_threshold_percentage' => 'required|numeric|min:0|max:100',
            'unit_price' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $supply = SupplyProfile::create([
                'supply_sku' => $this->supply_sku,
                'supply_item_class' => $this->supply_item_class,
                'item_type_id' => $this->item_type_id,
                'allocation_id' => $this->allocation_id,
                'supply_uom' => $this->supply_uom,
                'supply_description' => $this->supply_description,
                'supply_qty' => $this->supply_qty,
                'low_stock_threshold_percentage' => $this->low_stock_threshold_percentage,
                'supply_price1' => $this->unit_price,
                'unit_cost' => $this->unit_cost,
            ]);

            DB::commit();

            session()->flash('success', 'Product created successfully!');
            return redirect()->route('supplies.inventory');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.supplies.inventory.create');
    }
}
