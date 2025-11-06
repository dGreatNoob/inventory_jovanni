<?php

namespace App\Livewire\Pages\Allocation;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
#[Title('Allocation - Sales')]
class Sales extends Component
{
    public function render()
    {
        return view('livewire.pages.allocation.sales');
    }
}
