<?php

namespace App\Livewire\Pages\POManagement\PurchaseOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RawMatInv;

class ViewItem extends Component
{
    use WithPagination;

    public $poId;
    public $perPage = 10;

    public function mount($poId = null)
    {
        $this->poId = $poId ?? request()->get('poId');
    }

    public function render()
    {
        $rawMatInvs = RawMatInv::query()
            ->where('raw_mat_order_id', $this->poId)
            ->with(['purchaseOrder.supplier', 'purchaseOrder.department'])
            ->latest()
            ->paginate($this->perPage);


        return view('livewire.pages.POmanagement.purchase-order.index', [
            'rawMatInvs' => $rawMatInvs
        ]);
    }
}
