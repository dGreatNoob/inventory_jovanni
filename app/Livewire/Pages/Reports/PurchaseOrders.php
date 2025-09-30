<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use App\Models\PurchaseOrder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[
    Layout('components.layouts.app'),
    Title('Purchase Orders Report')
]
class PurchaseOrders extends Component
{
    public function render()
    {
        return view('livewire.pages.reports.purchase-orders');
    }
}