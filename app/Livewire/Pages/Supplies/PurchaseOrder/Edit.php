<?php

namespace App\Livewire\Pages\Supplies\PurchaseOrder;

use App\Models\SupplyProfile;
use App\Models\PurchaseOrder;
use App\Models\SupplyOrder;
use App\Models\Supplier;
use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class Edit extends Component
{
    use WithPagination;

    // Form properties
    public $purchaseOrder;
    public $Id;
    public $po_num;
    public $po_type = 'supply';
    public $ordered_by;

    #[Validate('required|exists:suppliers,id')]
    public $supplier_id;

    #[Validate('required|date')]
    public $order_date;
    
    #[Validate('required|date')]
    public $delivery_on;

    #[Validate('required|string|max:255')]
    public $payment_terms;

    #[Validate('required|string|max:255')]
    public $quotation;

    #[Validate('required|exists:departments,id')]
    public $deliver_to;

 

    // Item modal properties
    public $selected_supply_profile;
    public $unit_price;
    public $order_qty;

    #[Url(as: 'q')]
    public $search = '';

    public $showModal = false;
    public $editingItemIndex = null;

    // Cache for ordered items
    #[Validate('required|array|min:1')]
    public $orderedItems = [];

    public int $orderedItemsPerPage = 10;
    public $orderedItemsPage = 1;

    protected $messages = [
        'orderedItems.required' => 'Please add at least one item to the order.',
        'orderedItems.min' => 'Please add at least one item to the order.',
        'supplier_id.required' => 'Please select a supplier.',
        'supplier_id.exists' => 'The selected supplier is invalid.',
        'deliver_to.required' => 'Please select a receiving department.',
        'deliver_to.exists' => 'The selected department is invalid.',
        'selected_supply_profile.required' => 'Please select a supply profile.',
        'selected_supply_profile.exists' => 'The selected supply profile is invalid.',
        'unit_price.required' => 'Please enter the unit price.',
        'unit_price.numeric' => 'Unit price must be a number.',
        'unit_price.min' => 'Unit price must be greater than or equal to 0.',
        'order_qty.required' => 'Please enter the order quantity.',
        'order_qty.numeric' => 'Order quantity must be a number.',
        'order_qty.min' => 'Order quantity must be greater than or equal to 0.',
    ];

    protected $itemRules = [
        'selected_supply_profile' => 'required|exists:supply_profiles,id',
        'unit_price' => 'required|numeric|min:0',
        'order_qty' => 'required|numeric|min:0',
    ];

    public function mount($Id)
    {
        $this->Id = $Id;
        $this->purchaseOrder = PurchaseOrder::with(['supplier', 'supplyOrders.supplyProfile', 'orderedBy'])
            ->findOrFail($Id);

        if ($this->purchaseOrder->status !== 'pending') {
            session()->flash('error', 'Only pending purchase orders can be edited.');
            return redirect()->route('supplies.PurchaseOrder');
        }

        // Set form values
        $this->po_num = $this->purchaseOrder->po_num;
        $this->po_type = $this->purchaseOrder->po_type;
        $this->ordered_by = $this->purchaseOrder->orderedBy ? $this->purchaseOrder->orderedBy->name : '';
        $this->supplier_id = $this->purchaseOrder->supplier_id;
        $this->order_date = $this->purchaseOrder->order_date->format('Y-m-d');
        $this->delivery_on = $this->purchaseOrder->del_on->format('Y-m-d');
        $this->payment_terms = $this->purchaseOrder->payment_terms;
        $this->quotation = $this->purchaseOrder->quotation;
        $this->deliver_to = $this->purchaseOrder->del_to;

        // Set ordered items
        foreach ($this->purchaseOrder->supplyOrders as $order) {
            $this->orderedItems[] = [
                'id' => $order->id,
                'supply_profile_id' => $order->supply_profile_id,
                'description' => $order->supplyProfile->supply_description,
                'item_type' => $order->supplyProfile->item_type,
                'unit_price' => $order->unit_price,
                'order_qty' => $order->order_qty,
                'total_price' => $order->order_total_price,
            ];
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getSupplyProfilesProperty()
    {
        return SupplyProfile::with(['itemType'])
            ->where('supply_description', 'like', '%' . $this->search . '%')
            ->orWhereHas('itemType', function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('supply_item_class', 'like', '%' . $this->search . '%')
            ->paginate(5);
    }

    public function getDepartmentsProperty()
    {
        return Department::orderBy('name')->get();
    }

    public function getSuppliersProperty()
    {
        return Supplier::orderBy('name')->get();
    }

    public function selectSupplyProfile($id)
    {
        $this->selected_supply_profile = $id;
        $supply = SupplyProfile::find($id);
        $this->unit_price = $supply->latest_price;
    }

    public function editItem($index)
    {
        // Get the actual item from the paginated collection
        $items = $this->orderedItemsPaginated;
        if (!isset($items[$index])) {
            session()->flash('error', 'Invalid item index.');
            return;
        }

        // Find the actual index in the full orderedItems array
        $item = $items[$index];
        $actualIndex = array_search($item, $this->orderedItems);
        
        if ($actualIndex === false) {
            session()->flash('error', 'Item not found in the order.');
            return;
        }

        $this->editingItemIndex = $actualIndex;
        $this->selected_supply_profile = $item['supply_profile_id'];
        $this->unit_price = $item['unit_price'];
        $this->order_qty = $item['order_qty'];
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['selected_supply_profile', 'unit_price', 'order_qty', 'search', 'editingItemIndex']);
        $this->showModal = false;
    }

    public function addItem()
    {
        $this->validate($this->itemRules);

        $supply = SupplyProfile::find($this->selected_supply_profile);

        if ($this->editingItemIndex !== null) {
            // Update existing item
            $this->orderedItems[$this->editingItemIndex] = [
                'id' => $this->orderedItems[$this->editingItemIndex]['id'] ?? null,
                'supply_profile_id' => $supply->id,
                'description' => $supply->supply_description,
                'item_type' => $supply->item_type,
                'unit_price' => $this->unit_price,
                'order_qty' => $this->order_qty,
                'total_price' => $this->unit_price * $this->order_qty,
            ];
        } else {
            // Add new item
            $this->orderedItems[] = [
                'id' => null,
                'supply_profile_id' => $supply->id,
                'description' => $supply->supply_description,
                'item_type' => $supply->item_type,
                'unit_price' => $this->unit_price,
                'order_qty' => $this->order_qty,
                'total_price' => $this->unit_price * $this->order_qty,
            ];
        }

        // Reset modal fields
        $this->reset(['selected_supply_profile', 'unit_price', 'order_qty', 'search', 'editingItemIndex']);
        $this->showModal = false;
    }

    public function removeItem($index)
    {
        // Get the actual item from the paginated collection
        $items = $this->orderedItemsPaginated;
        if (!isset($items[$index])) {
            session()->flash('error', 'Invalid item index.');
            return;
        }

        // Find the actual index in the full orderedItems array
        $item = $items[$index];
        $actualIndex = array_search($item, $this->orderedItems);
        
        if ($actualIndex === false) {
            session()->flash('error', 'Item not found in the order.');
            return;
        }

        unset($this->orderedItems[$actualIndex]);
        $this->orderedItems = array_values($this->orderedItems);
        
        // If we removed the last item on the current page and it's not the first page,
        // go to the previous page
        if (empty($this->orderedItemsPaginated) && $this->orderedItemsPage > 1) {
            $this->orderedItemsPage--;
        }
    }

    public function submit()
    {
        if (empty($this->orderedItems)) {
            session()->flash('error', 'Please add at least one item to the list of items to order.');
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            // Calculate total price and quantity
            $total_price = 0;
            $total_qty = 0;
            foreach ($this->orderedItems as $item) {
                $total_price += $item['total_price'];
                $total_qty += $item['order_qty'];
            }

            // Update purchase order
            $this->purchaseOrder->update([
                'po_num' => $this->po_num,
                'total_price' => $total_price,
                'order_date' => $this->order_date,
                'del_to' => $this->deliver_to,
                'del_on' => $this->delivery_on,
                'supplier_id' => $this->supplier_id,
                'payment_terms' => $this->payment_terms,
                'quotation' => $this->quotation,
                'total_qty' => $total_qty,

            ]);

            // Get existing supply order IDs
            $existingOrderIds = $this->purchaseOrder->supplyOrders->pluck('id')->toArray();
            $updatedOrderIds = [];

            // Update or create supply orders
            foreach ($this->orderedItems as $item) {
                if (isset($item['id'])) {
                    // Update existing supply order
                    SupplyOrder::where('id', $item['id'])->update([
                        'supply_profile_id' => $item['supply_profile_id'],
                        'unit_price' => $item['unit_price'],
                        'order_total_price' => $item['total_price'],
                        'order_qty' => $item['order_qty'],
                    ]);
                    $updatedOrderIds[] = $item['id'];
                } else {
                    // Create new supply order
                    $newOrder = SupplyOrder::create([
                        'purchase_order_id' => $this->purchaseOrder->id,
                        'supply_profile_id' => $item['supply_profile_id'],
                        'unit_price' => $item['unit_price'],
                        'order_total_price' => $item['total_price'],
                        'final_total_price' => null,
                        'order_qty' => $item['order_qty'],
                        'received_qty' => null,
                        'first_remarks' => 'pending',
                        'final_remarks' => 'pending',
                    ]);
                    $updatedOrderIds[] = $newOrder->id;
                }
            }

            // Delete only the supply orders that were removed by the user
            $ordersToDelete = array_diff($existingOrderIds, $updatedOrderIds);
            if (!empty($ordersToDelete)) {
                SupplyOrder::whereIn('id', $ordersToDelete)->delete();
            }

            // Log activity
            $supplier = Supplier::find($this->supplier_id);
            $itemSummary = collect($this->orderedItems)->map(function($item) {
                $supply = SupplyProfile::find($item['supply_profile_id']);
                return 'SKU: ' . ($supply->supply_sku ?? '-') . ', Desc: ' . ($supply->supply_description ?? '-') . ', Qty: ' . $item['order_qty'] . ', Unit Price: ' . $item['unit_price'];
            })->implode('<br>');
            activity()
                ->causedBy(Auth::user())
                ->performedOn($this->purchaseOrder)
                ->withProperties([
                    'po_num' => $this->po_num,
                    'supplier' => $supplier ? $supplier->name : '-',
                    'total_price' => $total_price,
                    'items' => $itemSummary,
                ])
                ->log('Purchase order edited');

            DB::commit();

            session()->flash('success', 'Purchase order updated successfully.');
            return redirect()->route('supplies.PurchaseOrder');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to update purchase order: ' . $e->getMessage());
        }
    }

    public function getOrderedItemsPaginatedProperty()
    {
        $items = collect($this->orderedItems);
        $start = ($this->orderedItemsPage - 1) * $this->orderedItemsPerPage;
        return $items->slice($start, $this->orderedItemsPerPage);
    }

    public function getOrderedItemsTotalPagesProperty()
    {
        return ceil(count($this->orderedItems) / $this->orderedItemsPerPage);
    }

    public function updatedOrderedItemsPerPage()
    {
        $this->orderedItemsPage = 1;
    }

    public function nextOrderedItemsPage()
    {
        if ($this->orderedItemsPage < $this->orderedItemsTotalPages) {
            $this->orderedItemsPage++;
        }
    }

    public function previousOrderedItemsPage()
    {
        if ($this->orderedItemsPage > 1) {
            $this->orderedItemsPage--;
        }
    }

    public function goToOrderedItemsPage($page)
    {
        if ($page >= 1 && $page <= $this->orderedItemsTotalPages) {
            $this->orderedItemsPage = $page;
        }
    }

    public function render()
    {
        return view('livewire.pages.supplies.purchase-order.edit', [
            'supplyProfiles' => $this->supplyProfiles,
            'suppliers' => $this->suppliers,
            'departments' => $this->departments,
            'paginatedOrderedItems' => $this->orderedItemsPaginated,
            'orderedItemsTotalPages' => $this->orderedItemsTotalPages,
        ]);
    }
}

