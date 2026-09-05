@props([
    'notification' => null,
    'size' => null,
    'compact' => null,
])
@php

$data = data_get($notification, 'data', []);
$url = data_get($data, 'url');
$urlScheme = $url ? parse_url($url, PHP_URL_SCHEME) : null;
$url = ($url && ($urlScheme === null || in_array(strtolower($urlScheme), ['http', 'https']))) ? $url : null;
$as = $url ? 'a' : 'div';
$id = data_get($data, 'id') ?? data_get($notification, 'id');
$icon = data_get($data, 'icon') ?? data_get($notification, 'icon');
$title = data_get($data, 'title') ?? data_get($notification, 'title');
$message = data_get($data, 'message') ?? data_get($notification, 'message');
$type = data_get($data, 'type') ?? data_get($notification, 'type');
$created_at = Carbon\Carbon::parse(data_get($data, 'created_at') ?? data_get($notification, 'created_at'));
$read_at = data_get($data, 'read_at') ?? data_get($notification, 'read_at');

@endphp
<{{ $as }}
    @if ($url) href="{{ $url }}" @endif
    x-data="notificationItem"
    {{
        $attributes
            ->whereDoesntStartWith([
                'icon-container:', 'icon:',
                'content:', 'message:', 'time:',
                'actions:', 'bullet:', 'read:', 'remove:',
            ])
            ->classes(
                '
                    flex transition group
                    hover:bg-zinc-800/5 dark:hover:bg-zinc-800/80
                ',
                TALLKit::padding(size: $size, mode: $compact ? 'small' : null),
                TALLKit::gap(size: $size, mode: $compact ? null : 'largest'),
                TALLKit::roundedSize(size: $size, mode: 'large'),
            )
            ->dataKey('notification-item')
            ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId(prefix: 'notification-item', name: (string) $id)] : [], false)
    }}
>
    @if (!$compact && $icon !== false)
        <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-container:')->classes('shrink-0') }}>
            <tk:avatar
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')"
                :size="TALLKit::adjustSize(size: $size)"
                :icon="$icon ?? match ($type) {
                    'success' => 'check-circle-outline',
                    'error' => 'cancel-outline',
                    'warning' => 'warning-outline',
                    'info' => 'info-outline',
                    default => 'bell-outline',
                }"
                :color="match ($type) {
                    'success' => 'green',
                    'error' => 'red',
                    'warning' => 'amber',
                    'info' => 'blue',
                    default => 'filled',
                }"
                :user="false"
                square
            />
        </div>
    @endif

    <div
        {{
            TALLKit::attributesAfter(attributes: $attributes, prefix: 'content:')
                ->classes('flex-1 space-y-px')
        }}
    >
        <tk:text
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'message:')"
            :size="$compact ? TALLKit::adjustSize(size: $size) : $size"
        >
            {{ $message ?? $title ?? class_basename($type) }}
        </tk:text>
        <tk:text
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'time:')"
            :size="TALLKit::adjustSize(size: $size, move: $compact ? -2 : -1)"
            variant="subtle"
        >
            {{ $created_at->diffForHumans() }}
        </tk:text>
    </div>

    @if ($id)
        <div
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'actions:')
                    ->classes('shrink-0 w-fit ms-auto flex justify-end')
            }}
        >
            @if (! $read_at)
                <div
                    {{
                        TALLKit::attributesAfter(attributes: $attributes, prefix: 'bullet:')
                            ->classes(
                                match ($type) {
                                    'success' => 'bg-green-500',
                                    'error' => 'bg-red-500',
                                    'warning' => 'bg-amber-500',
                                    'info' => 'bg-blue-500',
                                    default => 'bg-green-500',
                                },
                                'rounded-full block group-hover:hidden',
                                TALLKit::widthHeight(size: $size, mode: 'smallest'),
                            )
                    }}
                ></div>

                <tk:button.group
                    :$size
                    class="hidden group-hover:flex"
                >
                    <tk:button
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'read:')->dataKey('dismissible')"
                        :size="TALLKit::adjustSize(size: $size, move: $compact ? -2 : -1)"
                        action="markNotificationAsRead({{ Js::from($id) }})"
                        icon="check-circle-outline"
                        tooltip="Mark as read"
                    />
                </tk:button.group>
            @else
                <tk:button.group :$size>
                    <tk:button
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'remove:')->dataKey('dismissible')"
                        :size="TALLKit::adjustSize(size: $size, move: $compact ? -2 : -1)"
                        action="deleteNotification({{ Js::from($id) }})"
                        icon="trash-outline"
                        tooltip="Remove notification"
                    />
                </tk:button.group>
            @endif
        </div>
    @endif
</{{ $as }}>
