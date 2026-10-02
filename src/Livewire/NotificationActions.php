<?php

namespace TALLKit\Livewire;

use function Livewire\on;

/** Macros: Livewire only calls a component's own public methods from the browser, so they go through this hook. */
class NotificationActions
{
    protected const MAX_IDS = 1000;

    protected const ACTIONS = ['notificationMarkAsRead' => 'id', 'notificationMarkAllAsRead' => 'ids', 'notificationDelete' => 'id'];

    public static function register()
    {
        on('call', function ($component, $method, $params, $componentContext, $returnEarly) {
            if (! array_key_exists($method, static::ACTIONS)) {
                return;
            }

            [$first, $guard] = array_pad(array_values($params), 2, null);
            $isId = fn ($id) => is_string($id) || is_int($id);

            $valid = match (static::ACTIONS[$method]) {
                'id' => $isId($first),
                'ids' => $first === null || (is_array($first) && array_is_list($first) && count($first) <= static::MAX_IDS && count($first) === count(array_filter($first, $isId))),
            };

            if (! $valid || ($guard !== null && ! is_string($guard))) {
                $returnEarly(null);

                return;
            }

            $params = [is_array($first) ? array_map('strval', $first) : ($first === null ? null : (string) $first), $guard];

            $returnEarly($component->{$method}(...$params));
        });
    }
}
