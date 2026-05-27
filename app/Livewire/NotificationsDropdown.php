<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationsDropdown extends Component
{
    private function getUser(): \App\Models\User
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        return $user;
    }

    // The properties can be read using $this->notifications
    public function getNotificationsProperty()
    {
        return $this->getUser()->unreadNotifications()->latest()->take(5)->get();
    }

    public function getUnreadCountProperty(): int
    {
        return $this->getUser()->unreadNotifications()->count();
    }

    public function markAsRead($notificationId)
    {
        $notification = $this->getUser()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        $this->getUser()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.notifications-dropdown');
    }
}
