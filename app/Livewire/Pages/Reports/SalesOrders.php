<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use App\Models\SalesOrder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[
    Layout('components.layouts.app'),
    Title('Sales Orders Report')
]
class SalesOrders extends Component
{
    public function render()
    {
        return view('livewire.pages.reports.sales-orders');
    }
}