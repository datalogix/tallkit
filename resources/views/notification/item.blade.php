@aware(['guard'])
@props([
    'notification' => null,
    'size' => null,
    'compact' => null,
    'actions' => false,
    'guard' => null,
])
@php

$data = data_get($notification, 'data', []);
$url = TALLKit::safeUrl(data_get($data, 'url'));
$id = data_get($data, 'id') ?? data_get($notification, 'id');
$icon = data_get($data, 'icon') ?? data_get($notification, 'icon');
$title = data_get($data, 'title') ?? data_get($notification, 'title');
$message = data_get($data, 'message') ?? data_get($notification, 'message');
$type = data_get($data, 'type') ?? data_get($notification, 'type');
$created_at = Carbon\Carbon::parse(data_get($data, 'created_at') ?? data_get($notification, 'created_at'));
$read_at = data_get($data, 'read_at') ?? data_get($notification, 'read_at');

@endphp
<div
    @if ($actions) x-data="notificationItem" @endif
    {{
        $attributes
            ->whereDoesntStartWith([
                'icon-container:', 'icon:',
                'content:', 'message:', 'description:', 'time:',
                'actions:', 'bullet:', 'read:', 'remove:',
            ])
            ->classes(
                '
                    relative flex transition group
                    hover:bg-zinc-800/5 dark:hover:bg-zinc-800/80
                    has-[[data-tallkit-notification-link]:focus-visible]:tk-focus-outline has-[[data-tallkit-notification-link]:focus-visible]:outline-offset-0
                ',
                TALLKit::padding(size: $size, mode: $compact ? 'small' : null),
                TALLKit::gap(size: $size, mode: $compact ? null : 'largest'),
                TALLKit::roundedSize(size: $size, mode: 'large'),
            )
            ->dataKey('notification-item')
            ->wireKey(TALLKit::generateId(prefix: 'notification-item', name: (string) $id))
    }}
>
    @if (! $compact && $icon !== false)
        <div {{ $attributes->prefixed('icon-container:')->classes('shrink-0') }}>
            <tk:avatar
                :attributes="$attributes->prefixed('icon:')"
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
            $attributes->prefixed('content:')
                ->classes('flex-1 space-y-px')
        }}
    >
        <tk:text
            :attributes="$attributes->prefixed('message:')->classes(['font-medium' => $title && $message])"
            :size="$compact ? TALLKit::adjustSize(size: $size) : $size"
        >
            @if ($url)
                <a
                    href="{{ $url }}"
                    {{ TALLKit::dataKey('notification-link') }}
                    class="outline-none after:absolute after:inset-0 after:rounded-[inherit]"
                >{{ $title ?? $message ?? class_basename($type) }}</a>
            @else
                {{ $title ?? $message ?? class_basename($type) }}
            @endif
        </tk:text>
        @if ($title && $message)
            <tk:text
                :attributes="$attributes->prefixed('description:')"
                :size="$compact ? TALLKit::adjustSize(size: $size, move: -1) : TALLKit::adjustSize(size: $size)"
                variant="subtle"
            >
                {{ $message }}
            </tk:text>
        @endif
        <tk:text
            :attributes="$attributes->prefixed('time:')"
            :size="TALLKit::adjustSize(size: $size, move: $compact ? -2 : -1)"
            variant="subtle"
        >
            {{ $created_at->diffForHumans() }}
        </tk:text>
    </div>

    @php($canAct = $actions && filled($id))
    @if (! $read_at || $canAct)
        <div
            {{
                $attributes->prefixed('actions:')
                    ->classes('relative z-10 shrink-0 w-fit ms-auto flex justify-end')
            }}
        >
            @if ($read_at)
                <tk:button.group :$size>
                    <tk:button
                        :attributes="$attributes->prefixed('remove:')->dataKey('dismissible')->merge(['tooltip' => 'Remove notification'])"
                        :size="TALLKit::adjustSize(size: $size, move: $compact ? -2 : -1)"
                        action="notificationDelete({{ Js::from($id) }}{{ $guard ? ', '.Js::from($guard) : '' }})"
                        icon="trash-outline"
                    />
                </tk:button.group>
            @else
                <div
                    {{
                        $attributes->prefixed('bullet:')
                            ->classes(
                                match ($type) {
                                    'success' => 'bg-green-600 dark:bg-green-500',
                                    'error' => 'bg-red-600 dark:bg-red-500',
                                    'warning' => 'bg-amber-600 dark:bg-amber-500',
                                    'info' => 'bg-blue-600 dark:bg-blue-500',
                                    default => 'bg-green-600 dark:bg-green-500',
                                },
                                ['rounded-full block', 'group-hover:hidden group-focus-within:hidden pointer-coarse:hidden' => $canAct],
                                TALLKit::widthHeight(size: $size, mode: 'smallest'),
                            )
                    }}
                ></div>

                @if ($canAct)
                <tk:button.group
                    :$size
                    class="sr-only group-hover:not-sr-only group-focus-within:not-sr-only pointer-coarse:not-sr-only"
                >
                    <tk:button
                        :attributes="$attributes->prefixed('read:')->dataKey('dismissible')->merge(['tooltip' => 'Mark as read'])"
                        :size="TALLKit::adjustSize(size: $size, move: $compact ? -2 : -1)"
                        action="notificationMarkAsRead({{ Js::from($id) }}{{ $guard ? ', '.Js::from($guard) : '' }})"
                        icon="check-circle-outline"
                    />
                </tk:button.group>
                @endif
            @endif
        </div>
    @endif
</div>
