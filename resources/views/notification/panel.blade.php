@props([
    'unread' => null,
    'read' => null,
    'unreadCount' => 0,
    'size' => null,
    'grouped' => null,
    'compact' => null,
    'tabs' => null,
    'markAll' => null,
])
@if ($tabs !== false)
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
                    @if ($unreadCount && $markAll !== false)
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
                        :$grouped
                        :$compact
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
                        :$grouped
                        :$compact
                        empty="No read notifications yet"
                    />
                </tk:tab.panel>
            </tk:tab.panels>
        </tk:section>
    </tk:tab.group>
@else
    <tk:section
        :attributes="TALLKit::attributesAfter($attributes, 'section:')"
        :size="TALLKit::adjustSize($size)"
        :title="$compact ? null : 'Notifications'"
        header:class="p-3 pb-0 items-center"
    >
        @if ($unreadCount && $markAll !== false)
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
            :$grouped
            :$compact
            empty="No new notifications"
        />
    </tk:section>
@endif
