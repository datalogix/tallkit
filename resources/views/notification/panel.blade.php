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
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'tab-group:')"
        :$size
    >
        <tk:section
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'section:')"
            :size="TALLKit::adjustSize()"
            title="Notifications"
            header:class="p-3 pb-0 items-center"
        >
            <x-slot:actions>
                <tk:tab.tabs
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'tab-items:')"
                    :size="TALLKit::adjustSize(size: $size)"
                    variant="segmented"
                >
                    <tk:tab
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'tab-unread:')"
                        :size="TALLKit::adjustSize(size: $size)"
                        name="unread"
                        label="Unread"
                        :badge="$unreadCount ? (string) min($unreadCount, 99) : null"
                        :badge:size="TALLKit::adjustSize(size: $size)"
                    />
                    <tk:tab
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'tab-read:')"
                        :size="TALLKit::adjustSize(size: $size)"
                        name="read"
                        label="Read"
                    />
                </tk:tab.tabs>
            </x-slot:actions>

            <tk:tab.panels
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'tab-panels:')"
            >
                <tk:tab.panel
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'tab-panel-unread:')"
                    name="unread"
                >
                    @if ($unreadCount && $markAll !== false)
                        <div class="flex justify-end px-2 pb-1 -mt-1">
                            <tk:button
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'mark-all:')->dataKey('notification-mark-all')"
                                :size="TALLKit::adjustSize(size: $size)"
                                action="markAllNotificationsAsRead()"
                                variant="none"
                                label="Mark all as read"
                            />
                        </div>
                    @endif

                    <tk:notification.list
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'list-unread:')"
                        :$size
                        :items="$unread"
                        :$grouped
                        :$compact
                        empty="No new notifications"
                    />
                </tk:tab.panel>

                <tk:tab.panel
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'tab-panel-read:')"
                    name="read"
                >
                    <tk:notification.list
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'list-read:')"
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
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'section:')"
        :size="TALLKit::adjustSize(size: $size)"
        :title="$compact ? null : 'Notifications'"
        header:class="p-3 pb-0 items-center"
    >
        @if ($unreadCount && $markAll !== false)
            <div class="flex justify-end px-2 pb-1 -mt-1">
                <tk:button
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'mark-all:')->dataKey('notification-mark-all')"
                    :size="TALLKit::adjustSize(size: $size)"
                    action="markAllNotificationsAsRead()"
                    variant="none"
                    label="Mark all as read"
                />
            </div>
        @endif

        <tk:notification.list
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'list-unread:')"
            :$size
            :items="$unread"
            :$grouped
            :$compact
            empty="No new notifications"
        />
    </tk:section>
@endif
