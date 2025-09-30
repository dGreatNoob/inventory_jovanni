<?php

namespace App\Livewire\Components;

use Livewire\Component;

class FlashMessage extends Component
{
     public $message;
    public $type = 'success'; // or 'error', etc.
    public $visible = false;

    protected $listeners = ['showFlashMessage'];

    public function showFlashMessage($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
        $this->visible = true;

        $this->dispatchBrowserEvent('auto-hide-flash');
    }

    public function render()
    {
        return view('livewire.components.flash-message');
    }
}
