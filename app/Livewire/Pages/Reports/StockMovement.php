<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[
    Layout('components.layouts.app'),
    Title('Stock Movement Report')
]
class StockMovement extends Component
{
    public function render()
    {
        return view('livewire.pages.reports.stock-movement');
    }
}