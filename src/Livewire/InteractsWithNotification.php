<?php

namespace TALLKit\Livewire;

trait InteractsWithNotification
{
    public function markNotificationAsRead()
    {
        return function (string $id) {
            auth()->user()?->notifications()->whereKey($id)->whereNull('read_at')->first()?->markAsRead();
        };
    }

    public function markAllNotificationsAsRead()
    {
        return function () {
            auth()->user()?->unreadNotifications()->update(['read_at' => now()]);
        };
    }

    public function deleteNotification()
    {
        return function (string $id) {
            auth()->user()?->notifications()->whereKey($id)->whereNotNull('read_at')->first()?->delete();
        };
    }
}
