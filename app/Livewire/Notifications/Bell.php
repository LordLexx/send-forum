<?php
namespace App\Livewire\Notifications;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Bell extends Component
{
    public function markAsRead()
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications->markAsRead();
        }
    }

    public function render()
    {
        $notifications = collect();
        $unreadCount = 0;

        if (Auth::check()) {
            $notifications = Auth::user()->notifications()->latest()->take(15)->get();
            $unreadCount = Auth::user()->unreadNotifications->count();
        }

        return view('livewire.notifications.bell', compact('notifications', 'unreadCount'));
    }
}
