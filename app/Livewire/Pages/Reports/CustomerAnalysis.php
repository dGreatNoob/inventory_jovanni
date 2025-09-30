<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use App\Models\CustomerProfile;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[
    Layout('components.layouts.app'),
    Title('Customer Analysis Report')
]
class CustomerAnalysis extends Component
{
    public function render()
    {
        return view('livewire.pages.reports.customer-analysis');
    }
}