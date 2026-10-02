@aware(['guard'])
@props([
    'unread' => null,
    'read' => null,
    'unreadCount' => 0,
    'size' => null,
    'grouped' => null,
    'compact' => null,
    'tabs' => null,
    'markAll' => null,
    'actions' => false,
    'guard' => null,
    'markAllIds' => null,
])
@if ($tabs !== false)
    <tk:tab.group
        :attributes="$attributes->prefixed('tab-group:')"
        :$size
    >
        <tk:section
            :attributes="$attributes->prefixed('section:')->merge(['title' => 'Notifications'])"
            :size="TALLKit::adjustSize()"
            header:class="p-3 pb-0 items-center"
        >
            <x-slot:actions>
                <tk:tab.tabs
                    :attributes="$attributes->prefixed('tabs:')"
                    :size="TALLKit::adjustSize(size: $size)"
                    variant="segmented"
                >
                    <tk:tab
                        :attributes="$attributes->prefixed('tab-unread:')->merge(['label' => 'Unread'])"
                        :size="TALLKit::adjustSize(size: $size)"
                        name="unread"
                        :badge="$unreadCount ? (string) min($unreadCount, 99) : null"
                        :badge:size="TALLKit::adjustSize(size: $size)"
                    />
                    <tk:tab
                        :attributes="$attributes->prefixed('tab-read:')->merge(['label' => 'Read'])"
                        :size="TALLKit::adjustSize(size: $size)"
                        name="read"
                    />
                </tk:tab.tabs>
            </x-slot:actions>

            <tk:tab.panels
                :attributes="$attributes->prefixed('tab-panels:')"
            >
                <tk:tab.panel
                    :attributes="$attributes->prefixed('tab-panel-unread:')"
                    name="unread"
                >
                    @if ($actions && $unreadCount && $markAll !== false)
                        <div class="flex justify-end px-2 pb-1 -mt-1">
                            <tk:button
                                :attributes="$attributes->prefixed('mark-all:')->dataKey('notification-mark-all')->merge(['label' => 'Mark all as read'])"
                                :size="TALLKit::adjustSize(size: $size)"
                                action="notificationMarkAllAsRead({{ Js::from($markAllIds) }}{{ $guard ? ', '.Js::from($guard) : '' }})"
                                variant="none"
                            />
                        </div>
                    @endif

                    <tk:notification.list
                        :attributes="$attributes->prefixed('list-unread:')->merge(['empty' => 'No new notifications'])"
                        :$size
                        :items="$unread"
                        :$actions
                        :$grouped
                        :$compact
                    />
                </tk:tab.panel>

                <tk:tab.panel
                    :attributes="$attributes->prefixed('tab-panel-read:')"
                    name="read"
                >
                    <tk:notification.list
                        :attributes="$attributes->prefixed('list-read:')->merge(['empty' => 'No read notifications yet'])"
                        :$size
                        :items="$read"
                        :$actions
                        :$grouped
                        :$compact
                    />
                </tk:tab.panel>
            </tk:tab.panels>
        </tk:section>
    </tk:tab.group>
@else
    <tk:section
        :attributes="$attributes->prefixed('section:')"
        :size="TALLKit::adjustSize(size: $size)"
        :title="$compact ? null : 'Notifications'"
        header:class="p-3 pb-0 items-center"
    >
        @if ($actions && $unreadCount && $markAll !== false)
            <div class="flex justify-end px-2 pb-1 -mt-1">
                <tk:button
                    :attributes="$attributes->prefixed('mark-all:')->dataKey('notification-mark-all')->merge(['label' => 'Mark all as read'])"
                    :size="TALLKit::adjustSize(size: $size)"
                    action="notificationMarkAllAsRead({{ Js::from($markAllIds) }}{{ $guard ? ', '.Js::from($guard) : '' }})"
                    variant="none"
                />
            </div>
        @endif

        <tk:notification.list
            :attributes="$attributes->prefixed('list-unread:')->merge(['empty' => 'No new notifications'])"
            :$size
            :items="$unread"
            :$actions
            :$grouped
            :$compact
        />
    </tk:section>
@endif
