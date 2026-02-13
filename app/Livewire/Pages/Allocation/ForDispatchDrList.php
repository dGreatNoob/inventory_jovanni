<?php

namespace App\Livewire\Pages\Allocation;

use App\Models\Box;
use App\Models\DeliveryReceipt;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
#[Title('Allocation - For Dispatch DRs')]
class ForDispatchDrList extends Component
{
    use WithPagination;

    public string $search = '';

    /** @var string 'all'|'not_dispatched'|'dispatched'|'in_shipment' */
    public string $statusFilter = 'all';

    public int $perPage = 10;

    /** Reset search and status filter to defaults. */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function getSummaryDRsProperty()
    {
        $query = DeliveryReceipt::with(['branchAllocation.branch', 'branchAllocation.batchAllocation', 'box'])
            ->where('type', 'mother')
            ->whereHas('box');

        if (trim($this->search) !== '') {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('dr_number', 'like', '%' . $term . '%')
                    ->orWhereHas('branchAllocation.branch', fn ($b) => $b->where('name', 'like', '%' . $term . '%')->orWhere('code', 'like', '%' . $term . '%'))
                    ->orWhereHas('branchAllocation.batchAllocation', fn ($ba) => $ba->where('ref_no', 'like', '%' . $term . '%'));
            });
        }

        if ($this->statusFilter === 'dispatched') {
            $query->whereHas('box', fn ($q) => $q->whereNotNull('dispatched_at'));
            $query->whereDoesntHave('shipmentVehicles');
        } elseif ($this->statusFilter === 'in_shipment') {
            $query->whereHas('shipmentVehicles');
        } elseif ($this->statusFilter === 'not_dispatched') {
            $query->whereHas('box', fn ($q) => $q->whereNull('dispatched_at'));
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage, ['*'], 'dr_page')
            ->through(function ($dr) {
                $branchAllocation = $dr->branchAllocation;
                $boxCount = $branchAllocation ? Box::where('branch_allocation_id', $branchAllocation->id)->count() : 0;
                $dispatched = $dr->box && $dr->box->dispatched_at !== null;
                $inShipment = $dr->shipmentVehicles()->exists();
                $status = $inShipment ? 'in_shipment' : ($dispatched ? 'dispatched' : 'not_dispatched');
                return (object) [
                    'id' => $dr->id,
                    'dr_number' => $dr->dr_number,
                    'created_at' => $dr->created_at,
                    'branch_name' => $branchAllocation?->branch?->name ?? '—',
                    'batch_ref' => $branchAllocation?->batchAllocation?->ref_no ?? '—',
                    'box_count' => $boxCount,
                    'status' => $status,
                    'dispatched_at' => $dispatched ? $dr->box?->dispatched_at : null,
                ];
            });
    }

    public function render()
    {
        return view('livewire.pages.allocation.for-dispatch-dr-list', [
            'summaryDRs' => $this->summaryDRs,
        ]);
    }
}
