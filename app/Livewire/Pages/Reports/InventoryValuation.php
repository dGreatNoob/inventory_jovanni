<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[
    Layout('components.layouts.app'),
    Title('Inventory Valuation Report')
]
class InventoryValuation extends Component
{
    public function render()
    {
        return view('livewire.pages.reports.inventory-valuation');
    }
}