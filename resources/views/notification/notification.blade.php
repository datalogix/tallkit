@props([
    'items' => null,
    'size' => null,
    'interval' => null,
    'variant' => null,
    'grouped' => null,
    'compact' => null,
    'tabs' => null,
    'markAll' => null,
    'guard' => null,
    'echo' => null,
    'limit' => 50,
])
@php

$user = $items === null ? auth($guard)->user() : null;
$own = $user && method_exists($user, 'notifications');
$all = collect($items ?? ($own ? $user->notifications()->latest()->when($limit, fn ($query, $limit) => $query->limit((int) $limit))->get() : []));
$isRead = fn ($notification) => ! is_null(data_get($notification, 'data.read_at') ?? data_get($notification, 'read_at'));
$unread = $all->reject($isRead)->values();
$read = $all->filter($isRead)->values();
$unreadCount = $own ? $user->unreadNotifications()->count() : $unread->count();
$markAllIds = $items === null ? null : $unread->map(fn ($notification) => data_get($notification, 'data.id') ?? data_get($notification, 'id'))->filter()->values()->all();

$component = TALLKit::livewireComponent();
$actions = (bool) $component;

$echoUser = $echo && $component ? auth($guard)->user() : null;
$channel = match (true) {
    ! $echoUser => null,
    method_exists($echoUser, 'receivesBroadcastNotificationsOn') => $echoUser->receivesBroadcastNotificationsOn(),
    default => str_replace('\\', '.', $echoUser::class).'.'.$echoUser->getKey(),
};

@endphp
<div
    x-data="notification({ channel: @js($channel) })"
    @if ($component && $interval) wire:poll.{{ max(1, (int) $interval) }}s @endif
    {{
        $attributes
            ->whereDoesntStartWith([
                'dropdown:', 'trigger:', 'popover:',
                'tab-', 'tabs:', 'section:', 'list-unread:', 'list-read:',
                'mark-all:'
            ])
            ->classes('contents')
    }}
>
    @if ($variant === 'inline')
        <tk:notification.panel
            :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['unreadCount' => null, 'markAll' => null, 'actions' => null, 'markAllIds' => null])"
            :$size
            :$unread
            :$read
            :$unreadCount
            :$grouped
            :$compact
            :$tabs
            :$markAll
        />
    @else
        <tk:dropdown :attributes="$attributes->prefixed('dropdown:')">
            <tk:button
                :attributes="$attributes->prefixed('trigger:')->merge([
                    'aria-label' => $unreadCount
                        ? __('Notifications (:count unread)', ['count' => $unreadCount])
                        : __('Notifications'),
                ])"
                :$size
                variant="subtle"
                icon="bell-outline"
                :iconDot="$unreadCount ? (string) min($unreadCount, 99) : null"
                icon-dot:class="bg-blue-500!"
                ::data-active="opened"
            />

            <tk:popover
                :attributes="$attributes->prefixed('popover:')
                    ->classes(
                        'w-full p-0 ',
                        $compact ? '[:where(&)]:max-w-xs' : '[:where(&)]:max-w-sm',
                        $compact ? '[:where(&)]:max-h-80' : '[:where(&)]:max-h-120',
                    )
                "
                :$size
                keep-open
            >
                <tk:notification.panel
                    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['unreadCount' => null, 'markAll' => null, 'actions' => null, 'markAllIds' => null])"
                    :$size
                    :$unread
                    :$read
                    :$unreadCount
                    :$grouped
                    :$compact
                    :$tabs
                    :$markAll
                />
            </tk:popover>
        </tk:dropdown>
    @endif
</div>
