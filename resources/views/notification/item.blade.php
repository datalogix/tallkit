@props([
    'notification' => null,
    'size' => null,
])
@php

$data = data_get($notification, 'data', []);
$url = data_get($data, 'url');
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
                'avatar-container:', 'avatar:',
                'content:', 'message:', 'time:',
                'actions:', 'bullet:', 'read:', 'remove:',
            ])
            ->classes(
                '
                    flex transition group
                    hover:bg-zinc-800/5 dark:hover:bg-zinc-800/80
                ',
                TALLKit::padding(size: $size),
                TALLKit::gap(size: $size, mode: 'largest'),
                TALLKit::roundedSize(size: $size, mode: 'large'),
            )
            ->dataKey('notification-item')
            ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId('notification-item', (string) $id)] : [], false)
    }}
>
    <div {{ TALLKit::attributesAfter($attributes, 'avatar-container:')->classes('shrink-0') }}>
        <tk:avatar
            :attributes="TALLKit::attributesAfter($attributes, 'avatar:')"
            :$size
            :icon="$icon ?? 'bell-outline'"
            :user="false"
            square
        />
    </div>

    <div
        {{
            TALLKit::attributesAfter($attributes, 'content:')
                ->classes('flex-1 space-y-px')
        }}
    >
        <tk:text
            :attributes="TALLKit::attributesAfter($attributes, 'message:')"
            :$size
        >
            {{ $message ?? $title ?? class_basename($type) }}
        </tk:text>
        <tk:text
            :attributes="TALLKit::attributesAfter($attributes, 'time:')"
            :size="TALLKit::adjustSize($size)"
            variant="subtle"
        >
            {{ $created_at->diffForHumans() }}
        </tk:text>
    </div>

    @if ($id)
        <div
            {{
                TALLKit::attributesAfter($attributes, 'actions:')
                    ->classes('shrink-0 w-fit ms-auto flex justify-end')
            }}
        >
            @if (! $read_at)
                <div
                    {{
                        TALLKit::attributesAfter($attributes, 'bullet:')
                            ->classes(
                                'bg-green-500 rounded-full block group-hover:hidden',
                                TALLKit::widthHeight(size: $size, mode: 'smallest'),
                            )
                    }}
                ></div>

                <tk:button.group
                    :$size
                    class="hidden group-hover:flex"
                >
                    <tk:button
                        :attributes="TALLKit::attributesAfter($attributes, 'read:')->dataKey('dismissible')"
                        :size="TALLKit::adjustSize($size)"
                        action="markNotificationAsRead('{{ $id }}')"
                        icon="check-circle-outline"
                        tooltip="Mark as read"
                    />
                </tk:button.group>
            @else
                <tk:button.group :$size>
                    <tk:button
                        :attributes="TALLKit::attributesAfter($attributes, 'remove:')->dataKey('dismissible')"
                        :size="TALLKit::adjustSize($size)"
                        action="deleteNotification('{{ $id }}')"
                        icon="trash-outline"
                        tooltip="Remove notification"
                    />
                </tk:button.group>
            @endif
        </div>
    @endif
</{{ $as }}>
