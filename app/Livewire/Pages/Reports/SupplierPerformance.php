<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use App\Models\SupplierProfile;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[
    Layout('components.layouts.app'),
    Title('Supplier Performance Report')
]
class SupplierPerformance extends Component
{
    public function render()
    {
        return view('livewire.pages.reports.supplier-performance');
    }
}