@props([
    'items' => null,
    'size' => null,
    'interval' => null,
    'variant' => null,
    'grouped' => null,
    'compact' => null,
    'tabs' => null,
    'markAll' => null,
])
@php

if ($items !== null) {
    $all = collect($items);
    $isRead = fn ($notification) => ! is_null(data_get($notification, 'data.read_at') ?? data_get($notification, 'read_at'));
    $unread = $all->reject($isRead)->values();
    $read = $all->filter($isRead)->values();
} else {
    $unread = collect(auth()->user()?->unreadNotifications ?? []);
    $read = collect(auth()->user()?->readNotifications ?? []);
}

$unreadCount = $unread->count();
$broadcasting = config('broadcasting.default') && config('broadcasting.default') !== 'null';

@endphp
<div
    x-data="notification({ channel: @js($broadcasting ? auth()->user()?->receivesBroadcastNotificationsOn() : null) })"
    @unless($broadcasting)
        wire:poll.{{ $interval ?? 30 }}s
    @endunless
    {{
        $attributes
            ->whereDoesntStartWith([
                'dropdown:', 'button:', 'popover:',
                'tab-', 'section:', 'list-unread:', 'list-read:',
                'mark-all:'
            ])
            ->classes('contents')
    }}
>
    @if ($variant === 'inline')
        <tk:notification.panel
            :attributes="$attributes"
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
        <tk:dropdown :attributes="TALLKit::attributesAfter($attributes, 'dropdown:')">
            <tk:button
                :attributes="TALLKit::attributesAfter($attributes, 'button:')"
                :$size
                variant="subtle"
                icon="bell-outline"
                :iconDot="$unreadCount ? (string) min($unreadCount, 99) : null"
                icon-dot:class="bg-blue-500!"
                ::data-active="opened"
            />

            <tk:popover
                :attributes="TALLKit::attributesAfter($attributes, 'popover:')
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
                    :attributes="$attributes"
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
