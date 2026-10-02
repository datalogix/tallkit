<?php

namespace TALLKit\Livewire\Mixins;

class NotificationMixin
{
    public function notificationMarkAsRead()
    {
        return function (string $id, ?string $guard = null) {
            $this->notificationUser($guard)?->notifications()->whereKey($id)->whereNull('read_at')->update(['read_at' => now()]);
        };
    }

    public function notificationMarkAllAsRead()
    {
        return function (?array $ids = null, ?string $guard = null) {
            $this->notificationUser($guard)?->unreadNotifications()
                ->when($ids !== null, fn ($query) => $query->whereKey($ids))
                ->update(['read_at' => now()]);
        };
    }

    public function notificationDelete()
    {
        return function (string $id, ?string $guard = null) {
            $this->notificationUser($guard)?->notifications()->whereKey($id)->whereNotNull('read_at')->delete();
        };
    }

    public function notificationUser()
    {
        return function (?string $guard = null) {
            if (property_exists($this, 'notificationGuard') && $this->notificationGuard !== null) {
                $guard = $this->notificationGuard;
            }

            // Only a guard the app has: it comes from the browser.
            if ($guard !== null && ! array_key_exists($guard, (array) config('auth.guards', []))) {
                return null;
            }

            $user = auth($guard)->user();

            return $user && method_exists($user, 'notifications') ? $user : null;
        };
    }
}
