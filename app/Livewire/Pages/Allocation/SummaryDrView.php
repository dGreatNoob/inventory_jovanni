<?php

namespace App\Livewire\Pages\Allocation;

use App\Models\Box;
use App\Models\BranchAllocationItem;
use App\Models\DeliveryReceipt;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
#[Title('Allocation - Summary DR')]
class SummaryDrView extends Component
{
    /** @var int Mother DR id from route */
    public int $summaryDr;

    public function mount(int $summaryDr): void
    {
        $this->summaryDr = $summaryDr;
    }

    /** Mother DR with relations; null if not found or not type mother */
    public function getMotherDrProperty(): ?DeliveryReceipt
    {
        $dr = DeliveryReceipt::with(['branchAllocation.branch', 'branchAllocation.batchAllocation', 'box'])
            ->where('id', $this->summaryDr)
            ->where('type', 'mother')
            ->first();
        return $dr;
    }

    /** Boxes for this summary DR's branch allocation, with DR number and item count */
    public function getBoxesWithItemsProperty(): \Illuminate\Support\Collection
    {
        $mother = $this->motherDr;
        if (!$mother || !$mother->branchAllocation) {
            return collect();
        }
        $branchAllocationId = $mother->branch_allocation_id;
        $boxes = Box::where('branch_allocation_id', $branchAllocationId)
            ->with('deliveryReceipts')
            ->orderBy('created_at')
            ->get();

        return $boxes->map(function ($box) {
            $dr = $box->deliveryReceipts->first();
            $items = BranchAllocationItem::where('box_id', $box->id)
                ->where('scanned_quantity', '>', 0)
                ->orderBy('product_snapshot_name')
                ->get();
            return (object) [
                'box' => $box,
                'dr_number' => $dr?->dr_number ?? '—',
                'items' => $items,
            ];
        });
    }

    /** Status label for the summary DR */
    public function getStatusLabelProperty(): string
    {
        $mother = $this->motherDr;
        if (!$mother || !$mother->box) {
            return '—';
        }
        if ($mother->shipmentVehicles()->exists()) {
            return 'In shipment';
        }
        return $mother->box->dispatched_at ? 'Dispatched' : 'Not dispatched';
    }

    public function render()
    {
        return view('livewire.pages.allocation.summary-dr-view');
    }
}
