@props([
    'items' => null,
    'size' => null,
    'interval' => null,
])
@php

$isRead = fn ($notification) => ! is_null(data_get($notification, 'data.read_at') ?? data_get($notification, 'read_at'));

if ($items !== null) {
    $all = collect($items);
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
                ->classes('w-full p-0 [:where(&)]:max-w-sm [:where(&)]:max-h-120')
            "
            :$size
            keep-open
        >
            <tk:tab.group
                :attributes="TALLKit::attributesAfter($attributes, 'tab-group:')"
                :$size
            >
                <tk:section
                    :attributes="TALLKit::attributesAfter($attributes, 'section:')"
                    :size="TALLKit::adjustSize($size)"
                    title="Notifications"
                    header:class="p-3 pb-0 items-center"
                >
                    <x-slot:actions>
                        <tk:tab.items
                            :attributes="TALLKit::attributesAfter($attributes, 'tab-items:')"
                            :size="TALLKit::adjustSize($size)"
                            variant="segmented"
                        >
                            <tk:tab
                                :attributes="TALLKit::attributesAfter($attributes, 'tab-unread:')"
                                :size="TALLKit::adjustSize($size)"
                                name="unread"
                                label="Unread"
                                :badge="$unreadCount ? (string) min($unreadCount, 99) : null"
                                :badge:size="TALLKit::adjustSize($size)"
                            />
                            <tk:tab
                                :attributes="TALLKit::attributesAfter($attributes, 'tab-read:')"
                                :size="TALLKit::adjustSize($size)"
                                name="read"
                                label="Read"
                            />
                        </tk:tab.items>
                    </x-slot:actions>

                    <tk:tab.panels
                        :attributes="TALLKit::attributesAfter($attributes, 'tab-panels:')"
                    >
                        <tk:tab.panel
                            :attributes="TALLKit::attributesAfter($attributes, 'tab-panel-unread:')"
                            name="unread"
                        >
                            @if ($unreadCount)
                                <div class="flex justify-end px-2 pb-1 -mt-1">
                                    <tk:button
                                        :attributes="TALLKit::attributesAfter($attributes, 'mark-all:')->dataKey('notification-mark-all')"
                                        :size="TALLKit::adjustSize($size)"
                                        action="markAllNotificationsAsRead()"
                                        variant="none"
                                        label="Mark all as read"
                                    />
                                </div>
                            @endif

                            <tk:notification.list
                                :attributes="TALLKit::attributesAfter($attributes, 'list-unread:')"
                                :$size
                                :items="$unread"
                                empty="No new notifications"
                            />
                        </tk:tab.panel>

                        <tk:tab.panel
                            :attributes="TALLKit::attributesAfter($attributes, 'tab-panel-read:')"
                            name="read"
                        >
                            <tk:notification.list
                                :attributes="TALLKit::attributesAfter($attributes, 'list-read:')"
                                :$size
                                :items="$read"
                                empty="No read notifications yet"
                            />
                        </tk:tab.panel>
                    </tk:tab.panels>
                </tk:section>
            </tk:tab.group>
        </tk:popover>
    </tk:dropdown>
</div>
