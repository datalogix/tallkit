@props([
    'type' => null,
    'icon' => null,
    'prepend' => null,
    'title' => null,
    'message' => null,
    'append' => null,
    'actions' => null,
    'border' => null,
    'closable' => null,
    'size' => null,
    'progress' => null,
    'duration' => null,
    'pauseOnHover' => null,
    'options' => null,
])
@php

$type = $type === 'error' ? 'danger' : $type;

$duration ??= $progress ? true : null;
$duration = match (true) {
    $duration === true, $duration === 'true' => 7000,
    is_numeric($duration) => max((int) $duration, 0),
    default => 0,
};

$palette = match ($type) {
    'danger' => 'red',
    'success' => 'green',
    'warning' => 'yellow',
    'info' => 'blue',
    default => 'zinc',
};

@endphp
<tk:content
    x-data="alertComponent({{ Js::from(($options ?? []) + [
        'duration' => $duration,
        'pauseOnHover' => $pauseOnHover ?? true,
    ]) }})"
    aria-atomic="true"
    :attributes="TALLKit::attributesMerge(
            $attributes->dataKey('alert')->whereDoesntStartWith(['message:', 'close:', 'progress:']),
            $attributes->prefixed('message:', keepPrefix: 'description:'),
        )
        ->merge([
            'role' => in_array($type, ['danger', 'warning']) ? 'alert' : 'status',
            'aria-live' => in_array($type, ['danger', 'warning']) ? 'assertive' : 'polite',
        ])
        ->classes(
            'relative overflow-hidden mb-4 transition-all duration-300 ease-out opacity-100',
            TALLKit::padding(size: $size),
            TALLKit::mutedBackground(color: $palette),
            TALLKit::mutedText(color: $palette),
            match ($border) {
                'top' => 'border-t-3',
                'left' => 'border-l-3',
                'right' => 'border-r-3',
                'bottom' => 'border-b-3',
                true => 'border',
                default => 'border-none',
            },
            match ($border) {
                'top', 'left', 'right', 'bottom' => '',
                default => TALLKit::roundedSize(size: $size, mode: 'small'),
            },
            match ($title && ($message || $slot->hasActualContent())) {
                true => 'items-start',
                default => 'items-center',
            },
        )
        ->when(
            $border,
            fn ($c) => $c->classes('border-(--tk-border)')
        )
    "
    :$size
    :icon="is_string($icon) || $icon === false ? $icon : match ($type) {
        'danger' => 'close-circle-outline',
        'success' => 'check-circle-outline',
        'warning' => 'warning-outline',
        default => 'info-circle-outline',
    }"
    icon:class="shrink-0"
    :$title
    title:scale="large"
    title:variant="none"
    :description="$message"
    description:variant="none"
    :$append
>
    {{ $slot }}

    @if ($prepend || $progress)
        <x-slot:prepend>
            {{ $prepend }}

            @if ($progress)
                <tk:alert.progress
                    :attributes="$attributes->prefixed('progress:')"
                    :type="is_string($progress) ? $progress : null"
                />
            @endif
        </x-slot:prepend>
    @endif

    @if ($actions || $closable)
        <x-slot:actions>
            {{ $actions }}

            @if ($closable)
                <tk:alert.close
                    :attributes="$attributes->prefixed('close:')"
                    :icon="is_string($closable) ? $closable : null"
                    :$size
                />
            @endif
        </x-slot:actions>
    @endif
</tk:content>
