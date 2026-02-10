<?php

namespace App\Livewire\Pages\Shipment;

use App\Models\DeliveryReceipt;
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

    // Summary DR selection (create path)
    public $availableSummaryDRs = [];
    public $selectedSummaryDrId = null;

    // Batch and Branch (edit path and legacy)
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

    public $showCreateSection = false;

    public function updatingSearch()        { $this->resetPage(); }
    public function updatingFilterStatus()  { $this->resetPage(); }
    public function updatingStatusFilter()  { $this->resetPage(); }
    public function updatedPerPage()        { $this->resetPage(); }

    public function openCreateSection()
    {
        $this->showCreateSection = true;
    }

    public function mount()
    {
        $this->deliveryMethods = Shipment::deliveryMethodDropDown();
        $date = now()->format('Ymd');
        $latest = Shipment::count() + 1;
        $this->shipping_plan_num = 'SHIP-' . $date . '-' . str_pad($latest, 3, '0', STR_PAD_LEFT);

        $this->salesOrders = SalesOrder::where('status', 'released')->with('customer')->get();

        $this->loadAvailableSummaryDRs();

        $summaryDrId = request()->query('summary_dr_id');
        if ($summaryDrId && $this->isEligibleSummaryDrId($summaryDrId)) {
            $this->selectedSummaryDrId = (int) $summaryDrId;
            $this->resetVehiclePlatesForSummaryDr();
        }
    }

    public function loadAvailableSummaryDRs()
    {
        $this->availableSummaryDRs = DeliveryReceipt::with(['branchAllocation.branch', 'branchAllocation.batchAllocation', 'box'])
            ->where('type', 'mother')
            ->whereHas('box', fn ($q) => $q->whereNotNull('dispatched_at'))
            ->whereDoesntHave('shipmentVehicles', function ($svQuery) {
                $svQuery->whereHas('shipment', fn ($s) => $s->whereIn('shipping_status', ['completed', 'cancelled', 'delivered']));
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    protected function isEligibleSummaryDrId($id): bool
    {
        return DeliveryReceipt::where('id', $id)
            ->where('type', 'mother')
            ->whereHas('box', fn ($q) => $q->whereNotNull('dispatched_at'))
            ->whereDoesntHave('shipmentVehicles', function ($svQuery) {
                $svQuery->whereHas('shipment', fn ($s) => $s->whereIn('shipping_status', ['completed', 'cancelled', 'delivered']));
            })
            ->exists();
    }

    public function updatedSelectedSummaryDrId($value = null)
    {
        $this->selectedSummaryDrId = $value ? (int) $value : null;
        $this->resetVehiclePlatesForSummaryDr();
    }

    protected function resetVehiclePlatesForSummaryDr()
    {
        $this->vehiclePlates = [];
        if (!$this->selectedSummaryDrId) {
            return;
        }
        $this->vehiclePlates[$this->selectedSummaryDrId] = '';
    }

    /** Selected Summary DR for preview (create path) */
    public function getSelectedSummaryDrProperty(): ?DeliveryReceipt
    {
        if (!$this->selectedSummaryDrId) {
            return null;
        }
        return DeliveryReceipt::with(['branchAllocation.branch', 'branchAllocation.batchAllocation', 'box'])
            ->find($this->selectedSummaryDrId);
    }

    /** Shipment being edited (edit path) */
    public function getEditingShipmentProperty(): ?Shipment
    {
        if (!$this->editValue) {
            return null;
        }
        return Shipment::with(['vehicles.deliveryReceipt', 'branchAllocation.branch'])->find($this->editValue);
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
                $this->showCreateSection   = true;
                $this->shipping_plan_num   = $result->shipping_plan_num;
                $this->scheduled_ship_date = $result->scheduled_ship_date;
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
        $rules = [
            'shipping_plan_num'   => ['required', 'string', 'max:255', Rule::unique('shipments', 'shipping_plan_num')->ignore($this->editValue)],
            'scheduled_ship_date' => ['required', 'date', function ($attribute, $value, $fail) {
                if (strtotime($value) < strtotime('today')) {
                    $fail('The scheduled ship date cannot be in the past.');
                }
            }],
            'delivery_method'     => 'required|string|max:255',
            'vehicle_plate_number'=> 'nullable|string|max:255',
        ];

        if (is_null($this->editValue)) {
            $rules['selectedSummaryDrId'] = ['required', 'integer', function ($attribute, $value, $fail) {
                if (!$this->isEligibleSummaryDrId($value)) {
                    $fail('The selected Summary DR is not valid or has already been assigned to a shipment.');
                }
            }];
        } else {
            $rules['selectedBranchId'] = ['required', 'exists:branch_allocations,id'];
        }

        $this->validate($rules);

        DB::beginTransaction();

        try {
            if (is_null($this->editValue)) {
                $motherDR = DeliveryReceipt::with('branchAllocation')->find($this->selectedSummaryDrId);
                if (!$motherDR || $motherDR->type !== 'mother') {
                    throw new \Exception('Invalid Summary DR selected.');
                }
                if (ShipmentVehicle::where('delivery_receipt_id', $motherDR->id)->exists()) {
                    throw new \Exception("DR {$motherDR->dr_number} has already been assigned to a shipment.");
                }

                $branchAllocation = $motherDR->branchAllocation;
                $plate = $this->vehiclePlates[$motherDR->id] ?? $this->vehicle_plate_number ?? null;

                $shipment = Shipment::create([
                    'shipping_plan_num'    => $this->shipping_plan_num,
                    'sales_order_id'       => null,
                    'batch_allocation_id'  => $branchAllocation->batch_allocation_id,
                    'branch_allocation_id' => $branchAllocation->id,
                    'delivery_receipt_id'  => $motherDR->id,
                    'customer_id'          => null,
                    'scheduled_ship_date'  => $this->scheduled_ship_date,
                    'delivery_method'      => $this->delivery_method,
                    'vehicle_plate_number' => $this->vehicle_plate_number,
                ]);

                ShipmentVehicle::create([
                    'shipment_id'         => $shipment->id,
                    'plate_number'        => $plate,
                    'delivery_receipt_id' => $motherDR->id,
                    'sort_order'          => 0,
                ]);

                $message = 'Shipment created successfully with 1 vehicle.';

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
            'selectedSummaryDrId',
            'selectedBatchId',
            'selectedBranchId',
            'selectedBranchAllocation',
            'branchReferenceNumbers',
            'availableBranches'
        ]);

        $date = now()->format('Ymd');
        $latest = Shipment::count() + 1;
        $this->shipping_plan_num = 'SHIP-' . $date . '-' . str_pad($latest, 3, '0', STR_PAD_LEFT);
        $this->loadAvailableSummaryDRs();
        $this->resetVehiclePlates();
        $this->resetVehiclePlatesForSummaryDr();
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