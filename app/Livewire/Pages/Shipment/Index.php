<?php

namespace App\Livewire\Pages\Shipment;

use App\Models\SalesOrder;
use App\Models\Shipment;
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
        'same-day'    => 'Same Day',
        'next-day'    => 'Next Day',
        'normal'      => 'Normal',
        'scheduled'   => 'Scheduled',
        'backorder'   => 'Backorder',
        'rush'        => 'Rush',
        'express'     => 'Express',
    ];

    public $perPage = 10;
    public $salesOrders;
    public $shipping_plan_num = '';
    public $scheduled_ship_date;
    public $delivery_method;
    public $vehicle_plate_number;
    public $shipping_priority = '';
    public $filterStatus = '';
    public $search = '';
    public $salesOrderResults = [];
    public $editValue = null;
    public $statusFilter = '';

    // Batch and Branch selection
    public $availableBatches = [];
    public $selectedBatchId = null;
    public $availableBranches = [];
    public $selectedBranchIds = [];
    public $branchReferenceNumbers = [];

    public $deliveryMethods = [
        'courier',
        'pickup',
        'truck',
        'motorbike',
        'in-house',
        'cargo',
    ];

    public function updatingSearch()     { $this->resetPage(); }
    public function updatingFilterStatus(){ $this->resetPage(); }

    public function mount()
    {
        $this->deliveryMethods = Shipment::deliveryMethodDropDown();
        $date = now()->format('Ymd');
        $latest = Shipment::count() + 1;
        $this->shipping_plan_num = 'SHIP-' . $date . '-' . str_pad($latest, 3, '0', STR_PAD_LEFT);

        $this->salesOrders = SalesOrder::where('status', 'released')->with('customer')->get();

        $this->loadAvailableBatches();
    }

    public function loadAvailableBatches()
    {
        $this->availableBatches = BatchAllocation::with(['branchAllocations.branch'])
            ->where('status', 'dispatched')
            ->whereDoesntHave('branchAllocations', function($query) {
                $query->whereHas('shipments', function($query) {
                    $query->whereIn('shipping_status', ['completed', 'cancelled', 'delivered']);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updatedSelectedBatchId($value = null)
    {
        $this->selectedBatchId = $value ?? $this->selectedBatchId;
        $this->loadAvailableBranches();
        // Automatically select all branch Allocations for this batch
        $this->selectedBranchIds = collect($this->availableBranches)->pluck('id')->toArray();
        // Optionally auto-fill branch reference numbers if needed
        foreach ($this->availableBranches as $branchAlloc) {
            $this->branchReferenceNumbers[$branchAlloc->id] = $branchAlloc->reference_number ?? null;
        }
    }

    public function loadAvailableBranches()
    {
        if (!$this->selectedBatchId) {
            $this->availableBranches = [];
            return;
        }
        $this->availableBranches = BranchAllocation::with('branch')
            ->where('batch_allocation_id', $this->selectedBatchId)
            ->get()
            ->all();
    }

    public function edit($id)
    {
        $result = Shipment::with('customer')->find($id);

        if ($result) {
            if ($result->shipping_status == 'pending') {
                $this->shipping_plan_num    = $result->shipping_plan_num;
                $this->scheduled_ship_date  = $result->scheduled_ship_date;
                $this->vehicle_plate_number = $result->vehicle_plate_number;
                $this->delivery_method      = $result->delivery_method;
                $this->editValue            = $id;

                // Branch selection for edit
                $this->selectedBatchId      = $result->batch_allocation_id;
                $this->selectedBranchIds    = [$result->branch_allocation_id];
                $this->loadAvailableBranches();
            } else {
                session()->flash('error', 'You can only edit shipments that are in pending status.');
            }
        }
    }

    public function updatedStatusFilter($value)
    {
        $this->statusFilter = $value;
    }

    public function createShipment()
    {
        $this->validate([
            'shipping_plan_num'   => ['required', 'string', 'max:255', Rule::unique('shipments', 'shipping_plan_num')->ignore($this->editValue)],
            'scheduled_ship_date' => ['required', 'date', function ($attribute, $value, $fail) {
                if (strtotime($value) < strtotime('today')) {
                    $fail('The scheduled ship date cannot be in the past.');
                }
            }],
            'delivery_method'     => 'required|string|max:255',
            'vehicle_plate_number'=> 'nullable|string|max:255',
            'selectedBranchIds'   => 'required|array|min:1',
            'selectedBranchIds.*' => 'exists:branch_allocations,id',
        ]);

        DB::beginTransaction();

        try {
            if (is_null($this->editValue)) {
                // Create new shipments for each branch allocation
                foreach ($this->selectedBranchIds as $branchAllocId) {
                    $branchAllocation = BranchAllocation::find($branchAllocId);

                    Shipment::create([
                        'shipping_plan_num'    => $this->branchReferenceNumbers[$branchAllocId] ?? $this->shipping_plan_num,
                        'sales_order_id'       => null,
                        'batch_allocation_id'  => $branchAllocation->batch_allocation_id,
                        'branch_allocation_id' => $branchAllocId,
                        'customer_id'          => null,
                        'scheduled_ship_date'  => $this->scheduled_ship_date,
                        'delivery_method'      => $this->delivery_method,
                        'vehicle_plate_number' => $this->vehicle_plate_number,
                    ]);
                }
            } else {
                $shipment = Shipment::find($this->editValue);
                $branchAllocation = BranchAllocation::find($this->selectedBranchIds[0] ?? null);
                $shipmentData = [
                    'shipping_plan_num'    => $this->shipping_plan_num,
                    'sales_order_id'       => null,
                    'batch_allocation_id'  => $branchAllocation?->batch_allocation_id,
                    'branch_allocation_id' => $this->selectedBranchIds[0] ?? null,
                    'customer_id'          => null,
                    'scheduled_ship_date'  => $this->scheduled_ship_date,
                    'delivery_method'      => $this->delivery_method,
                    'vehicle_plate_number' => $this->vehicle_plate_number,
                ];
                $shipment->update($shipmentData);
            }

            DB::commit();

            session()->flash('success', 'Shipment created successfully.');
            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('shipment_error', $e->getMessage());
        }

        // You may want further logic here for each branch allocation if needed.
    }

    protected function resetForm()
    {
        $this->reset([
            'shipping_plan_num',
            'scheduled_ship_date',
            'delivery_method',
            'vehicle_plate_number',
            'editValue',
            'selectedBatchId',
            'selectedBranchIds',
            'branchReferenceNumbers',
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
            'shipped_at'      => now(),
        ]);
    }

    public function markAsDelivered($id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->update([
            'shipping_status' => 'delivered',
            'delivered_at'    => now(),
        ]);
    }

    public function render()
    {
        $query = Shipment::with('customer');

        return view('livewire.pages.shipment.index', [
            'shipments' => $query->search($this->search)
                ->filterStatus($this->statusFilter)
                ->latest()
                ->paginate($this->perPage)
        ]);
    }
}