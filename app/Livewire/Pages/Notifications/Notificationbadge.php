<?php

namespace App\Livewire\Pages\Notifications;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notificationbadge extends Component
{
    public $count = 0;

    public function mount()
    {
        $this->count = Auth::user()->unreadNotifications->count();
    }

    public function render()
    {
        return view('livewire.pages.notifications.notificationbadge');
    }
}
