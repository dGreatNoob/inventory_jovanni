<?php

namespace App\Livewire\Pages\Shipment;

use App\Models\SalesOrder;
use App\Models\Shipment;
use App\Models\StockMovement;
use App\Models\BatchAllocation;
use App\Models\BranchAllocation;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class Index extends Component
{
    use WithPagination; 
    
    public $shippingPriorityDropdown = [
        'same-day'=> 'Same Day', // For deliveries within the same day
        'next-day'=> 'Next Day', // Promises delivery on the following day
        'normal'  => 'Normal',   // Default, regular delivery  
        'scheduled'=>'Scheduled' , //Customer chooses a specific delivery date/time
        'backorder' => 'Backorder', // Delivery delayed until stock is available
        'rush'    => 'Rush', // Prioritized processing and delivery
        'express' => 'Express', // Express – Fastest available delivery
    ];

    public $showQrModal = false;
    public $getShipmentDetails = null;
    public $perPage = 10;
    public $salesOrders;
    public $shipping_plan_num = '';
    public $customer_name;
    public $customer_address;
    public $scheduled_ship_date;
    public $delivery_method;
    public $vehicle_plate_number;
    public $shipping_priority = '';
    public $filterStatus = '';
    public $search = '';
    public $salesOrderResults = [];
    public $phone = '';
    public $editValue = null;
    public $statusFilter = '';

    // Batch and Branch selection
    public $availableBatches = [];
    public $selectedBatchId = null;
    public $availableBranches = [];
    public $selectedBranchId = null;
        
    public $deliveryMethods = [
        'courier', 
        'pickup', 
        'truck', 
        'motorbike', 
        'in-house', 
        'cargo'
    ];


    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    public function loadAvailableBatches()
    {
        $this->availableBatches = BatchAllocation::with(['branchAllocations.branch'])
            ->where('status', 'dispatched')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function loadAvailableBranches()
    {
        if (!$this->selectedBatchId) {
            $this->availableBranches = [];
            return;
        }

        $this->availableBranches = BranchAllocation::with('branch')
            ->where('batch_allocation_id', $this->selectedBatchId)
            ->get();
    }

    public function updatedSelectedBatchId()
    {
        $this->selectedBranchId = null;
        $this->availableBranches = [];
        $this->loadAvailableBranches();
    }

    public function mount()
    {
        $this->deliveryMethods = Shipment::deliveryMethodDropDown();
        $date = now()->format('Ymd');
        $latest = Shipment::count() + 1;
        $this->shipping_plan_num = 'SHIP-' . $date . '-' . str_pad($latest, 3, '0', STR_PAD_LEFT);

        // Get all sales orders marked as 'released' (after stock-out), including customer info
        $this->salesOrders = SalesOrder::where('status', 'released')->with('customer')->get();

        $this->loadAvailableBatches();
    }
    
    public function edit($id){

        $results = Shipment::with('customer')->find($id);

        if($results){
            if($results->shipping_status == 'pending'){
                $this->shipping_plan_num = $results->shipping_plan_num;
                $this->scheduled_ship_date = $results->scheduled_ship_date;
                $this->vehicle_plate_number = $results->vehicle_plate_number;
                $this->customer_name = $results->customer_name;
                $this->customer_address = $results->customer_address;
                $this->phone = $results->customer_phone;
                $this->delivery_method = $results->delivery_method;
                $this->editValue = $id;
            }else{
                session()->flash('error', 'You can only edit shipments that are in pending status.');
            }
        }
    }

    public function showShipmentQrCode($shipping_plan_num)
    {
        $this->showQrModal = true;
        $this->getShipmentDetails = Shipment::where('shipping_plan_num',$shipping_plan_num)
            ->with(['branchAllocation.items.product'])
            ->first();
    }

    public function updatedStatusFilter($value){
        $this->statusFilter = $value;
    }
 

    public function createShipment()
    {
        $this->validate([
            'shipping_plan_num' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shipments', 'shipping_plan_num')->ignore($this->editValue)
            ],
            'scheduled_ship_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (strtotime($value) < strtotime('today')) {
                        $fail('The scheduled ship date cannot be in the past.');
                    }
                },
            ],
            'delivery_method' => 'required|string|max:255',
            'vehicle_plate_number' => 'nullable|string|max:255',
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|digits:11',
            'customer_address' => 'required|string|max:255',
            'selectedBranchId' => 'required|exists:branch_allocations,id',
        ]);

        DB::beginTransaction();

        try {
            if($this->editValue == null){
                $branchAllocation = BranchAllocation::find($this->selectedBranchId);
                Shipment::create([
                    'shipping_plan_num' => $this->shipping_plan_num,
                    'sales_order_id' => null,
                    'batch_allocation_id' => $branchAllocation->batch_allocation_id,
                    'branch_allocation_id' => $this->selectedBranchId,
                    'customer_id' => null,
                    'customer_name' => $this->customer_name,
                    'customer_address' => $this->customer_address,
                    'scheduled_ship_date' => $this->scheduled_ship_date,
                    'delivery_method' => $this->delivery_method,
                    'vehicle_plate_number' => $this->vehicle_plate_number,
                    'customer_phone' => $this->phone,
                ]);
            }else{

                $Shipment = Shipment::find($this->editValue);
                $branchAllocation = BranchAllocation::find($this->selectedBranchId);
                $ShipmentData = [
                    'shipping_plan_num' => $this->shipping_plan_num,
                    'sales_order_id' => null,
                    'batch_allocation_id' => $branchAllocation->batch_allocation_id,
                    'branch_allocation_id' => $this->selectedBranchId,
                    'customer_id' => null,
                    'customer_name' => $this->customer_name,
                    'customer_address' => $this->customer_address,
                    'scheduled_ship_date' => $this->scheduled_ship_date,
                    'delivery_method' => $this->delivery_method,
                    'vehicle_plate_number' => $this->vehicle_plate_number,
                    'customer_phone' => $this->phone
                ];

                $Shipment->update($ShipmentData);

            }

            DB::commit();

            session()->flash('success', 'Shipment created successfully.');
            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('shipment_error', $e->getMessage());
        }
    }

    public function closeQrModal()
    {
        $this->showQrModal = false;
        $this->getShipmentDetails = null;
    }

    protected function resetForm()
    {
        $this->reset([
            'shipping_plan_num',
            'customer_name',
            'customer_address',
            'scheduled_ship_date',
            'delivery_method',
            'vehicle_plate_number',
            'editValue',
            'selectedBatchId',
            'selectedBranchId',
            'availableBranches'
        ]);
        $date = now()->format('Ymd');
        $latest = Shipment::count() + 1;
        $this->shipping_plan_num = 'SHIP-' . $date . '-' . str_pad($latest, 3, '0', STR_PAD_LEFT);
    }

    public function markAsShipped($id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->update([
            'shipping_status' => 'shipped',
            'shipped_at' => now(),
        ]);
    }

    public function markAsDelivered($id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->update([
            'shipping_status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function render()
    {
        $query = Shipment::with('customer');

        return view('livewire.pages.shipment.index', [
            'shipments' => $query->search( $this->search )->filterStatus($this->statusFilter)
                ->latest()
                ->paginate($this->perPage)
        ]);
    }
}