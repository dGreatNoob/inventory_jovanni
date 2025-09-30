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

class Create extends Component
{
    use WithPagination;

    // Form properties
    public $po_type = 'supply';
    public $ordered_by;
    public $po_num = '<NEW>';

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

    public function mount()
    {
        $this->ordered_by = Auth::user()->name;
        $this->order_date = date('Y-m-d');
        $this->delivery_on = date('Y-m-d');
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
        $this->updateUnitPrice();
    }

    private function updateUnitPrice()
    {
        if ($this->selected_supply_profile) {
            $supply = SupplyProfile::find($this->selected_supply_profile);
            if ($supply) {
                $this->unit_price = $supply->unit_cost;
            }
        }
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

        $supply = SupplyProfile::with(['itemType'])->find($this->selected_supply_profile);

        if ($this->editingItemIndex !== null) {
            // Update existing item
            $this->orderedItems[$this->editingItemIndex] = [
                'supply_profile_id' => $supply->id,
                'description' => $supply->supply_description,
                'item_type' => $supply->itemType->name,
                'unit_price' => $this->unit_price,
                'order_qty' => $this->order_qty,
                'total_price' => $this->unit_price * $this->order_qty,
            ];
        } else {
            // Add new item
            $this->orderedItems[] = [
                'supply_profile_id' => $supply->id,
                'description' => $supply->supply_description,
                'item_type' => $supply->itemType->name,
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
        unset($this->orderedItems[$index]);
        $this->orderedItems = array_values($this->orderedItems);
    }

    private function generatePONumber()
    {
        $lastPO = PurchaseOrder::orderBy('po_num', 'desc')
            ->first();

        return $lastPO ? $lastPO->po_num + 1 : 6000;
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

            // Generate PO number
            $po_num = $this->generatePONumber();

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'status' => 'for_approval',
                'po_num' => $po_num,
                'total_price' => $total_price,
                'order_date' => $this->order_date,
                'del_on' => $this->delivery_on,
                'del_to' => $this->deliver_to,
                'supplier_id' => $this->supplier_id,
                'payment_terms' => $this->payment_terms,
                'quotation' => $this->quotation,
                'total_est_weight' => null,
                'total_received_weight' => null,
                'po_type' => $this->po_type,
                'total_qty' => $total_qty,
                'ordered_by' => Auth::id(),
            ]);

            // Create supply orders
            foreach ($this->orderedItems as $item) {
                SupplyOrder::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'supply_profile_id' => $item['supply_profile_id'],
                    'unit_price' => $item['unit_price'],
                    'order_total_price' => $item['total_price'],
                    'final_total_price' => null,
                    'order_qty' => $item['order_qty'],
                    'received_qty' => null,
                    'first_remarks' => 'pending',
                    'final_remarks' => 'pending',
                ]);
            }

            // Log activity
            $supplier = Supplier::find($this->supplier_id);
            $department = Department::find($this->deliver_to);
            $itemSummary = collect($this->orderedItems)->map(function($item) {
                $supply = SupplyProfile::find($item['supply_profile_id']);
                return 'SKU: ' . ($supply->supply_sku ?? '-') . ', Desc: ' . ($supply->supply_description ?? '-') . ', Qty: ' . $item['order_qty'] . ', Price: ' . $item['unit_price'];
            })->implode('<br>');
            $description =
                'Supplier: ' . ($supplier ? $supplier->name : '-') . '<br>' .
                'Receiving Dept: ' . ($department ? $department->name : '-') . '<br>' .
                'Payment Terms: ' . $this->payment_terms . '<br>' .
                'Quotation: ' . $this->quotation . '<br>' .
                'Items:<br>' . $itemSummary;
            activity()
                ->causedBy(Auth::user())
                ->performedOn($purchaseOrder)
                ->event('created')
                ->withProperties([
                    'po_num' => $po_num,
                    'supplier' => $supplier ? $supplier->name : '-',
                    'department' => $department ? $department->name : '-',
                    'payment_terms' => $this->payment_terms,
                    'quotation' => $this->quotation,
                    'total_price' => $total_price,
                    'status' => 'for_approval',
                    'items' => $itemSummary,
                ])
                ->log($description ? $description : 'Purchase order created and pending approval');

            DB::commit();

            session()->flash('success', 'Purchase order submitted for approval.');
            return redirect()->route('supplies.PurchaseOrder');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create purchase order: ' . $e->getMessage());
        }
    }

    public function getOrderedItemsPaginatedProperty()
    {
        $items = collect($this->orderedItems)->reverse();
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
        return view('livewire.pages.supplies.purchase-order.create', [
            'supplyProfiles' => $this->supplyProfiles,
            'suppliers' => $this->suppliers,
            'departments' => $this->departments,
            'paginatedOrderedItems' => $this->orderedItemsPaginated,
            'orderedItemsTotalPages' => $this->orderedItemsTotalPages,
        ]);
    }
}
