<?php

namespace App\Livewire\Pages\Shipment;

use App\Models\SalesOrder;
use App\Models\Shipment;
use App\Models\ShipmentVehicle;
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
    /** @var array<int, string> Plate number per mother DR id (for new creation) */
    public $vehiclePlates = [];
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
    public $selectedBranchId = null;
    public $selectedBranchAllocation = null;
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
            ->whereHas('branchAllocations', function($query) {
                $query->whereHas('deliveryReceipts', function($drQuery) {
                    $drQuery->where('type', 'mother')
                            ->whereHas('box', fn ($boxQuery) => $boxQuery->whereNotNull('dispatched_at'))
                            ->whereDoesntHave('shipmentVehicles', function($svQuery) {
                                $svQuery->whereHas('shipment', fn ($s) => $s->whereIn('shipping_status', ['completed', 'cancelled', 'delivered']));
                            });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updatedSelectedBatchId($value = null)
    {
        $this->selectedBatchId = $value ?? $this->selectedBatchId;
        $this->loadAvailableBranches();
        $this->selectedBranchId = null; // Reset branch selection when batch changes
        $this->selectedBranchAllocation = null;
    }

    public function updatedSelectedBranchId($value = null)
    {
        $this->selectedBranchId = $value ?? $this->selectedBranchId;
        if ($this->selectedBranchId) {
            $this->selectedBranchAllocation = BranchAllocation::with('branch', 'items.product')->find($this->selectedBranchId);
            $this->resetVehiclePlates();
        } else {
            $this->selectedBranchAllocation = null;
            $this->vehiclePlates = [];
        }
    }

    protected function resetVehiclePlates()
    {
        $this->vehiclePlates = [];
        if (!$this->selectedBranchId) {
            return;
        }
        $motherDRs = \App\Models\DeliveryReceipt::where('branch_allocation_id', $this->selectedBranchId)
            ->where('type', 'mother')
            ->whereHas('box', fn ($q) => $q->whereNotNull('dispatched_at'))
            ->orderBy('created_at')
            ->get();
        foreach ($motherDRs as $dr) {
            $this->vehiclePlates[$dr->id] = '';
        }
    }

    public function loadAvailableBranches()
    {
        if (!$this->selectedBatchId) {
            $this->availableBranches = [];
            return;
        }

        // Only show branches that have dispatched mother DRs not yet in completed shipments
        $this->availableBranches = BranchAllocation::with('branch')
            ->where('batch_allocation_id', $this->selectedBatchId)
            ->whereHas('deliveryReceipts', function($query) {
                $query->where('type', 'mother')
                      ->whereHas('box', fn ($boxQuery) => $boxQuery->whereNotNull('dispatched_at'))
                      ->whereDoesntHave('shipmentVehicles', function($svQuery) {
                          $svQuery->whereHas('shipment', fn ($s) => $s->whereIn('shipping_status', ['completed', 'cancelled', 'delivered']));
                      });
            })
            ->get()
            ->all();
    }

    public function edit($id)
    {
        $result = Shipment::with(['customer', 'vehicles.deliveryReceipt'])->find($id);

        if ($result) {
            if ($result->shipping_status == 'pending') {
                $this->shipping_plan_num    = $result->shipping_plan_num;
                $this->scheduled_ship_date  = $result->scheduled_ship_date;
                $this->vehicle_plate_number = $result->vehicle_plate_number;
                $this->delivery_method      = $result->delivery_method;
                $this->editValue            = $id;

                $this->vehiclePlates = [];
                foreach ($result->vehicles as $v) {
                    if ($v->delivery_receipt_id) {
                        $this->vehiclePlates[$v->delivery_receipt_id] = $v->plate_number ?? '';
                    }
                }

                $this->selectedBatchId      = $result->batch_allocation_id;
                $this->selectedBranchId     = $result->branch_allocation_id;
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
            'selectedBranchId'    => ['required', 'exists:branch_allocations,id'],
        ]);

        DB::beginTransaction();

        try {
            if (is_null($this->editValue)) {
                $branchAllocation = BranchAllocation::find($this->selectedBranchId);

                $motherDRs = \App\Models\DeliveryReceipt::where('branch_allocation_id', $this->selectedBranchId)
                    ->where('type', 'mother')
                    ->whereHas('box', fn ($q) => $q->whereNotNull('dispatched_at'))
                    ->orderBy('created_at')
                    ->get();

                if ($motherDRs->isEmpty()) {
                    throw new \Exception('No dispatched mother DRs found for this branch allocation.');
                }

                // Check if any DR already has a shipment vehicle
                foreach ($motherDRs as $motherDR) {
                    if (ShipmentVehicle::where('delivery_receipt_id', $motherDR->id)->exists()) {
                        throw new \Exception("DR {$motherDR->dr_number} has already been assigned to a shipment.");
                    }
                }

                // Create ONE shipment
                $shipment = Shipment::create([
                    'shipping_plan_num'    => $this->shipping_plan_num,
                    'sales_order_id'       => null,
                    'batch_allocation_id'  => $branchAllocation->batch_allocation_id,
                    'branch_allocation_id' => $this->selectedBranchId,
                    'delivery_receipt_id'  => $motherDRs->first()->id, // Keep for backward compat
                    'customer_id'          => null,
                    'scheduled_ship_date'  => $this->scheduled_ship_date,
                    'delivery_method'      => $this->delivery_method,
                    'vehicle_plate_number' => $this->vehicle_plate_number,
                ]);

                // Create N shipment vehicles (one per mother DR)
                foreach ($motherDRs as $index => $motherDR) {
                    $plate = $this->vehiclePlates[$motherDR->id] ?? $this->vehicle_plate_number ?? null;
                    ShipmentVehicle::create([
                        'shipment_id'          => $shipment->id,
                        'plate_number'         => $plate,
                        'delivery_receipt_id'  => $motherDR->id,
                        'sort_order'           => $index,
                    ]);
                }

                $vehicleCount = $motherDRs->count();
                $message = $vehicleCount === 1
                    ? 'Shipment created successfully with 1 vehicle.'
                    : "Shipment created successfully with {$vehicleCount} vehicles.";

            } else {
                $shipment = Shipment::find($this->editValue);
                $branchAllocation = BranchAllocation::find($this->selectedBranchId);
                $shipment->update([
                    'shipping_plan_num'    => $this->shipping_plan_num,
                    'sales_order_id'       => null,
                    'batch_allocation_id'  => $branchAllocation?->batch_allocation_id,
                    'branch_allocation_id' => $this->selectedBranchId,
                    'customer_id'          => null,
                    'scheduled_ship_date'  => $this->scheduled_ship_date,
                    'delivery_method'      => $this->delivery_method,
                    'vehicle_plate_number' => $this->vehicle_plate_number,
                ]);
                // Update vehicle plates from vehiclePlates array
                foreach ($shipment->vehicles as $vehicle) {
                    $plate = $this->vehiclePlates[$vehicle->delivery_receipt_id] ?? null;
                    if ($plate !== null) {
                        $vehicle->update(['plate_number' => $plate]);
                    }
                }
                $message = 'Shipment updated successfully.';
            }

            DB::commit();

            session()->flash('success', $message);
            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('shipment_error', $e->getMessage());
        }
    }

    protected function resetForm()
    {
        $this->reset([
            'shipping_plan_num',
            'scheduled_ship_date',
            'delivery_method',
            'vehicle_plate_number',
            'vehiclePlates',
            'editValue',
            'selectedBatchId',
            'selectedBranchId',
            'selectedBranchAllocation',
            'branchReferenceNumbers',
            'availableBranches'
        ]);

        $date = now()->format('Ymd');
        $latest = Shipment::count() + 1;
        $this->shipping_plan_num = 'SHIP-' . $date . '-' . str_pad($latest, 3, '0', STR_PAD_LEFT);
        $this->resetVehiclePlates();
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
        $query = Shipment::with(['customer', 'branchAllocation.branch', 'deliveryReceipt', 'vehicles.deliveryReceipt']);

        return view('livewire.pages.shipment.index', [
            'shipments' => $query->search($this->search)
                ->filterStatus($this->statusFilter)
                ->latest()
                ->paginate($this->perPage)
        ]);
    }
}