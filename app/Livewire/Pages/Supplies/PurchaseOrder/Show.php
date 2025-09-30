<?php

namespace App\Livewire\Pages\Supplies\PurchaseOrder;

use App\Models\PurchaseOrder;
use Livewire\Component;

class Show extends Component
{
    public $Id;

    public function mount($Id)
    {
        $this->Id = $Id;
        $purchaseOrder = PurchaseOrder::findOrFail($Id);
        
        // Route to appropriate component based on status
        switch ($purchaseOrder->status) {
            case 'for_approval':
                return redirect()->route('supplies.PurchaseOrder.showForApproval', $Id);
            case 'received':
                return redirect()->route('supplies.PurchaseOrder.showReceivingReport', $Id);
            default:
                return redirect()->route('supplies.PurchaseOrder.showStandard', $Id);
        }
    }

    public function render()
    {
        // This should never be reached due to redirects in mount()
        return view('livewire.pages.supplies.purchase-order.show');
    }
}
