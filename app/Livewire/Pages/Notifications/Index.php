<?php

namespace App\Livewire\Pages\Notifications;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $notifications;
    public $unreadCount;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();
        $this->notifications = $user->notifications;
        $this->unreadCount = $user->unreadNotifications->count();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }
    
    public function render()
    {
        return view('livewire.pages.notifications.index');
    }
}
