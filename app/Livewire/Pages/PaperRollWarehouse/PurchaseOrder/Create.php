<?php

namespace App\Livewire\Pages\PaperRollWarehouse\PurchaseOrder;

use App\Models\RawMatProfile;
use App\Models\PurchaseOrder;
use App\Models\RawMatOrder;
use App\Models\RawMatInv;
use App\Models\Supplier;
use App\Models\Department;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class Create extends Component
{
    use WithPagination;

    public $po_type = 'raw_mats';
    public $ordered_by;
    public $supplier_id;
    public $order_date;
    public $payment_terms;
    public $quotation;
    public $deliver_to;

    public $selected_rawmat_profile;
    public $unit_price;
    public $order_qty;
    public $search = '';
    public $showModal = false;
    public $showViewModal = false;

    public $orderedItems = [];
    public $departments = [];

    public $orderedItemsPerPage = 10;
    public $orderedItemsPage = 1;

    protected $rules = [
        'supplier_id' => 'required|exists:suppliers,id',
        'order_date' => 'required|date',
        'payment_terms' => 'required',
        'quotation' => 'required',
        'deliver_to' => 'required',
        'orderedItems' => 'required|array|min:1',
    ];

    public function mount()
    {
        $this->departments = Department::all();
        $this->ordered_by = Auth::user()->name;
        $this->order_date = date('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function getRawmatProfilesProperty()
    {
        return RawMatProfile::where('gsm', 'like', '%' . $this->search . '%')
        ->orWhere('classification', 'like', '%' . $this->search . '%')
        ->orWhere('supplier', 'like', '%' . $this->search . '%')
        ->paginate(5);
    }

    public function getSuppliersProperty()
    {
        return Supplier::orderBy('name')->get();
    }

    public function selectRawmatProfile($id)
    {
        $this->selected_rawmat_profile = $id;
        $rawmat = RawMatProfile::find($id);
    }
    
    public function addItem()
    {
        $this->validate([
            'selected_rawmat_profile' => 'required',
            'order_qty' => 'required|numeric|min:0',
        ]);

        $rawmat = RawMatProfile::find($this->selected_rawmat_profile);

        $this->orderedItems[] = [
            'raw_mat_profile_id' => $rawmat->id,
            'gsm' => $rawmat->gsm,
            'width_size' => $rawmat->width_size,
            'classification' => $rawmat->classification,
            'supplier' => $rawmat->supplier,
            'country_origin' => $rawmat->country_origin,
            'order_qty' => $this->order_qty,
            'total_price' => 25 * $this->order_qty,
        ];

        $this->reset(['selected_rawmat_profile', 'order_qty', 'search']);
        $this->showModal = false;
    }

    public function removeItem($index)
    {
        unset($this->orderedItems[$index]);
        $this->orderedItems = array_values($this->orderedItems);
    }

    public function submit() 
    { 
        if (empty($this->orderedItems)) { 
            session()->flash('error', 'Please add at least one item.'); 
            return; 
        } 

        $this->validate(); 

        try { 
            DB::beginTransaction(); 

            // Generate unique PO number starting from 6000 
            $lastPo = PurchaseOrder::latest()->first(); 
            $poNum = $lastPo ? $lastPo->po_num + 1 : 60000; 

            $total_price = 0; 
            $total_qty = 0; 
            foreach ($this->orderedItems as $item) { 
                $total_price += $item['total_price']; 
                $total_qty += $item['order_qty']; 
            } 

            $purchaseOrder = PurchaseOrder::create([ 
                'status' => 'pending', 
                'total_price' => $total_price, 
                'order_date' => $this->order_date, 
                'del_on' => null, 
                'del_to' => $this->deliver_to,
                'supplier_id' => $this->supplier_id, 
                'payment_terms' => $this->payment_terms, 
                'quotation' => $this->quotation, 
                'total_est_weight' => null, 
                'po_type' => $this->po_type, 
                'total_qty' => $total_qty, 
                'ordered_by' => Auth::id(),
                'po_num' => $poNum, 
            ]); 

            // Starting SPC number 
            $lastSpc = RawMatInv::orderByDesc('spc_num')->first(); 
            $spcBase = $lastSpc ? $lastSpc->spc_num + 1 : 400000; 

            foreach ($this->orderedItems as $item) { 
                $rawmatorder = RawMatOrder::create([ 
                    'purchase_order_id' => $purchaseOrder->id, 
                    'raw_mat_profile_id' => $item['raw_mat_profile_id'], 
                ]); 

                for ($i = 0; $i < $item['order_qty']; $i++) { 
                    $uniqueSupplierNum = 'BCM' . now()->format('YmdHis') . $i . rand(10, 99); // Generates something like BCM202506011630101287

                    RawMatInv::create([ 
                        'spc_num' => $spcBase++, 
                        'supplier_num' => $uniqueSupplierNum, 
                        'status' => 'pending', 
                        'weight' => 0, 
                        'rem_weight' => 0, 
                        'remarks' => 'pending', 
                        'raw_mat_order_id' => $rawmatorder->id, 
                    ]); 
                } 
            }

            DB::commit(); 
            session()->flash('success', 'Raw material purchase order created successfully.'); 
            return redirect()->route('prw.purchaseorder'); 

        } catch (\Exception $e) { 
            DB::rollBack(); 
            session()->flash('error', 'Error: ' . $e->getMessage()); 
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
        return view('livewire.pages.paper-roll-warehouse.purchase-order.create', [
            'rawmatProfiles' => $this->getRawmatProfilesProperty(),
            'suppliers' => $this->getSuppliersProperty(),
            'paginatedOrderedItems' => $this->orderedItemsPaginated,
            'orderedItemsTotalPages' => $this->orderedItemsTotalPages,
        ]);
    }
}