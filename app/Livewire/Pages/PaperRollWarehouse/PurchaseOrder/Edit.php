<?php

namespace App\Livewire\Pages\PaperRollWarehouse\PurchaseOrder;

use App\Models\RawMatProfile;
use App\Models\RawMatInv;
use App\Models\PurchaseOrder;
use App\Models\RawMatOrder;
use App\Models\Supplier;
use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Edit extends Component
{
    use WithPagination;

    // Form properties
    public $purchaseOrder;
    public $Id;
    public $po_num;
    public $po_type = 'raw_mats';
    public $ordered_by;

    #[Validate('required|exists:suppliers,id')]
    public $supplier_id;

    #[Validate('required|date')]
    public $order_date;
    
    #[Validate('required')]
    public $approver;

    #[Validate('required|string|max:255')]
    public $payment_terms;

    #[Validate('required|string|max:255')]
    public $quotation;

    #[Validate('required|exists:departments,id')]
    public $deliver_to;

    // Item modal properties
    public $selected_rawmat_profile;
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
        'selected_rawmat_profile.required' => 'Please select a raw material profile.',
        'selected_rawmat_profile.exists' => 'The selected raw material profile is invalid.',
        'order_qty.required' => 'Please enter the order quantity.',
        'order_qty.numeric' => 'Order quantity must be a number.',
        'order_qty.min' => 'Order quantity must be greater than 0.',
    ];

    protected $itemRules = [
        'selected_rawmat_profile' => 'required|exists:raw_mat_profiles,id',
        'order_qty' => 'required|numeric|min:1',
    ];

    public function mount($Id)
    {
        $this->Id = $Id;
        $this->purchaseOrder = PurchaseOrder::with(['supplier', 'rawMatOrders.rawMatProfile', 'orderedBy'])
            ->findOrFail($Id);

        if ($this->purchaseOrder->status !== 'pending') {
            session()->flash('error', 'Only pending purchase orders can be edited.');
            return redirect()->route('prw.purchaseorder');
        }

        // Set form values
        $this->po_num = $this->purchaseOrder->po_num;
        $this->po_type = $this->purchaseOrder->po_type;
        $this->ordered_by = $this->purchaseOrder->orderedBy ? $this->purchaseOrder->orderedBy->name : '';
        $this->supplier_id = $this->purchaseOrder->supplier_id;
        $this->order_date = $this->purchaseOrder->order_date->format('Y-m-d');
        $this->approver = $this->purchaseOrder->approver;
        $this->payment_terms = $this->purchaseOrder->payment_terms;
        $this->quotation = $this->purchaseOrder->quotation;
        $this->deliver_to = $this->purchaseOrder->del_to;

        // Set ordered items
        foreach ($this->purchaseOrder->rawMatOrders as $order) {
            $this->orderedItems[] = [
                'id' => $order->id,
                'raw_mat_profile_id' => $order->raw_mat_profile_id,
                'classification' => $order->rawMatProfile->classification,
                'gsm' => $order->rawMatProfile->gsm,
                'width_size' => $order->rawMatProfile->width_size,
                'supplier' => $order->rawMatProfile->supplier,
                'country_origin' => $order->rawMatProfile->country_origin,
                'order_qty' => $order->rawMatInvs->count()
            ];
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function getRawMatProfilesProperty()
    {
        return RawMatProfile::where('supplier', 'like', '%' . $this->search . '%')
            ->orWhere('classification', 'like', '%' . $this->search . '%')
            ->orWhere('gsm', 'like', '%' . $this->search . '%')
            ->orWhere('width_size', 'like', '%' . $this->search . '%')
            ->orWhere('country_origin', 'like', '%' . $this->search . '%')
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

    public function selectRawMatProfile($id)
    {
        $this->selected_rawmat_profile = $id;
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
        $this->selected_rawmat_profile = $item['raw_mat_profile_id'];
        $this->order_qty = $item['order_qty'];
        $this->showModal = true;
    }
    
    public function closeModal()
    {
        $this->reset(['selected_rawmat_profile', 'order_qty', 'search', 'editingItemIndex']);
        $this->showModal = false;
    }

    public function addItem()
    {
        $this->validate($this->itemRules);

        $rawmat = RawMatProfile::find($this->selected_rawmat_profile);

        if ($this->editingItemIndex !== null) {
            // Update existing item
            $this->orderedItems[$this->editingItemIndex] = [
                'id' => $this->orderedItems[$this->editingItemIndex]['id'] ?? null,
                'raw_mat_profile_id' => $rawmat->id,
                'classification' => $rawmat->classification,
                'gsm' => $rawmat->gsm,
                'width_size' => $rawmat->width_size,
                'supplier' => $rawmat->supplier,
                'country_origin' => $rawmat->country_origin,
                'order_qty' => $this->order_qty
            ];
        } else {
            // Add new item - check for duplicates
            $exists = collect($this->orderedItems)->firstWhere('raw_mat_profile_id', $rawmat->id);
            if ($exists) {
                session()->flash('error', 'This raw material is already in the order list.');
                return;
            }

            $this->orderedItems[] = [
                'id' => null,
                'raw_mat_profile_id' => $rawmat->id,
                'classification' => $rawmat->classification,
                'gsm' => $rawmat->gsm,
                'width_size' => $rawmat->width_size,
                'supplier' => $rawmat->supplier,
                'country_origin' => $rawmat->country_origin,
                'order_qty' => $this->order_qty
            ];
        }

        // Reset modal fields
        $this->reset(['selected_rawmat_profile', 'order_qty', 'search', 'editingItemIndex']);
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

            // Calculate total quantity
            $total_qty = 0;
            foreach ($this->orderedItems as $item) {
                $total_qty += $item['order_qty'];
            }

            // Update purchase order
            $this->purchaseOrder->update([
                'po_num' => $this->po_num,
                'order_date' => $this->order_date,
                'del_to' => $this->deliver_to,
                // 'del_on' => $this->delivery_on,
                'supplier_id' => $this->supplier_id,
                'payment_terms' => $this->payment_terms,
                'quotation' => $this->quotation,
                'total_qty' => $total_qty,
            ]);

            $lastSpc = RawMatInv::orderByDesc('spc_num')->first();
            $spcBase = $lastSpc ? $lastSpc->spc_num + 1 : 400000;

            foreach ($this->orderedItems as $item) {
                $order = RawMatOrder::with('rawMatInvs')->find($item['id']);
                if ($order) {
                    $order->update([
                        'raw_mat_profile_id' => $item['raw_mat_profile_id'],
                    ]);

                    $currentCount = $order->rawMatInvs->count();
                    $desiredCount = $item['order_qty'];
                    $difference = $desiredCount - $currentCount;

                    if ($difference < 0) {
                        // Too many, delete extras
                        $toDelete = $order->rawMatInvs()->latest()->take(abs($difference))->get();
                        foreach ($toDelete as $inv) {
                            $inv->delete();
                        }
                    } elseif ($difference > 0) {
                        // Too few, create more
                        for ($i = 0; $i < $difference; $i++) {
                            RawMatInv::create([
                                'spc_num' => $spcBase++,
                                'supplier_num' => 1,
                                'status' => 'pending',
                                'weight' => 1,
                                'rem_weight' => 1,
                                'remarks' => 'pending',
                                'raw_mat_order_id' => $order->id,
                            ]);
                        }
                    }
                }
            }

            // Get existing raw material order IDs
            $existingOrderIds = $this->purchaseOrder->rawMatOrders->pluck('id')->toArray();
            $updatedOrderIds = [];

            // Update or create raw material orders
            foreach ($this->orderedItems as $item) {
                if (isset($item['id']) && $item['id']) {
                    // Update existing raw material order
                    RawMatOrder::where('id', $item['id'])->update([
                        'raw_mat_profile_id' => $item['raw_mat_profile_id'],
                        // 'order_qty' => $item['order_qty'],
                    ]);
                    $updatedOrderIds[] = $item['id'];
                } else {
                    // Create new raw material order
                    $newOrder = RawMatOrder::create([
                        'purchase_order_id' => $this->purchaseOrder->id,
                        'raw_mat_profile_id' => $item['raw_mat_profile_id'],
                        // 'order_qty' => $item['order_qty'],
                    ]);
                    $updatedOrderIds[] = $newOrder->id;
                }
            }

            // Delete raw material orders that were removed by the user
            $ordersToDelete = array_diff($existingOrderIds, $updatedOrderIds);
            if (!empty($ordersToDelete)) {
                RawMatOrder::whereIn('id', $ordersToDelete)->delete();
            }

            DB::commit();

            session()->flash('success', 'Purchase order updated successfully.');
            return redirect()->route('prw.purchaseorder');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to update purchase order: ' . $e->getMessage());
        }
    }

    public function getOrderedItemsPaginatedProperty()
    {
        $items = collect($this->orderedItems);
        $start = ($this->orderedItemsPage - 1) * $this->orderedItemsPerPage;
        return $items->slice($start, $this->orderedItemsPerPage)->values();
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
        return view('livewire.pages.paper-roll-warehouse.purchase-order.edit', [
            'rawMatProfiles' => $this->rawMatProfiles,
            'suppliers' => $this->suppliers,
            'departments' => $this->departments,
            'paginatedOrderedItems' => $this->orderedItemsPaginated,
            'orderedItemsTotalPages' => $this->orderedItemsTotalPages,
        ]);
    }
}