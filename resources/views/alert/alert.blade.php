@props([
    'type' => null,
    'variant' => null,
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
])
@php

$type = $type === 'error' ? 'danger' : $type;

$duration ??= $progress ? true : null;
$duration = match (true) {
    $duration === true, $duration === 'true' => true,
    is_numeric($duration) => max((int) $duration, 0),
    is_string($duration) && preg_match('/^\s*(\d+(?:\.\d+)?)\s*(ms|s)\s*$/i', $duration, $matches) => (int) round($matches[1] * (strtolower($matches[2]) === 's' ? 1000 : 1)),
    default => 0,
};

$progress = $duration === true || $duration > 0 ? $progress : null;

$palette = match ($type) {
    'danger' => 'red',
    'success' => 'green',
    'warning' => 'yellow',
    'info' => 'blue',
    default => 'zinc',
};

$variant = in_array($variant, ['outline', 'subtle'], true) ? $variant : 'soft';
$border ??= $variant === 'outline' ? true : null;

@endphp
<tk:content
    x-data="alertComponent({{ Js::from([
        'duration' => $duration,
        'pauseOnHover' => $pauseOnHover ?? true,
    ]) }})"
    aria-atomic="true"
    :role="in_array($type, ['danger', 'warning']) ? 'alert' : 'status'"
    :aria-live="in_array($type, ['danger', 'warning']) ? 'assertive' : 'polite'"
    :attributes="TALLKit::attributesMerge(
            $attributes->dataKey('alert')->whereDoesntStartWith(['message:', 'close:', 'progress:']),
            $attributes->prefixed('message:', keepPrefix: 'description:'),
        )
        ->classes(
            'relative overflow-hidden transition-all duration-300 ease-out opacity-100',
            TALLKit::padding(size: $size),
            match ($variant) {
                'outline' => 'bg-transparent '.TALLKit::textNeutral(),
                'subtle' => TALLKit::backgroundNeutral('faint').' '.TALLKit::textNeutral(),
                default => TALLKit::mutedBackground(color: $palette).' '.TALLKit::mutedText(color: $palette),
            },
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
            'items-start',
        )
        ->when(
            $border,
            fn ($c) => $c->classes($variant === 'soft' ? 'border-(--tk-border)' : TALLKit::border(color: $palette))
        )
    "
    :$size
    :icon="is_string($icon) || $icon === false ? $icon : match ($type) {
        'danger' => 'close-circle-outline',
        'success' => 'check-circle-outline',
        'warning' => 'warning-outline',
        default => 'info-circle-outline',
    }"
    :icon:class="TALLKit::classes('shrink-0', $variant === 'soft' ? null : TALLKit::text(color: $palette))"
    :$title
    title:scale="large"
    title:variant="none"
    :description="$message"
    description:variant="none"
    :$append
>
    {{ $slot }}

    @if (has_content($prepend) || $progress)
        <x-slot:prepend>
            {{ $prepend }}

            @if ($progress)
                <tk:progress
                    :attributes="$attributes->prefixed('progress:')
                        ->dataKey('alert-progress')
                        ->except('bar:class')
                    "
                    :bar:class="TALLKit::classes(
                        match (true) {
                            ! in_array($progress, ['top', 'bottom'], true) => 'bg-black/5 dark:bg-white/10',
                            $variant === 'soft' => 'bg-current/40',
                            default => TALLKit::background(color: $palette),
                        },
                        $attributes->get('progress:bar:class'),
                    )"
                    :overlay="in_array($progress, ['top', 'bottom'], true) ? $progress : 'fill'"
                    variable="progressValue"
                />
            @endif
        </x-slot:prepend>
    @endif

    @if (has_content($actions) || $closable)
        <x-slot:actions>
            {{ $actions }}

            @if ($closable)
                <tk:alert.close
                    :attributes="$attributes->prefixed('close:')
                        ->when($variant !== 'soft', fn ($attrs) => $attrs->classes('text-current', TALLKit::backgroundNeutral('faint', 'hover:')))
                    "
                    :icon="string_value($closable)"
                    :$size
                />
            @endif
        </x-slot:actions>
    @endif
</tk:content>
