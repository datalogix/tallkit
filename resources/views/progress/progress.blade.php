@props([
    'value' => null,
    'variable' => null,
    'size' => null,
    'variant' => null,
    'color' => null,
    'position' => null,
    'label' => null,
    'square' => null,
    'overlay' => null,
])
@php

$displayValue = $variable ? "Math.max(0, Math.min(100, Number({$variable}) || 0))" : 'value';
$overlay = in_array($overlay, ['top', 'bottom', 'fill'], true) ? $overlay : null;

if ($overlay) {
    $square ??= true;
    $position = 'none';
    $label ??= false;
}

$decorative = $label === false;

@endphp
<div
    {{
        $attributes->prefixed('container:')
            ->unless($variable, fn ($attrs) => $attrs->merge(['x-data' => 'progress('.Js::from($value ?? 0).')']))
            ->when($decorative, fn ($attrs) => $attrs->merge(['aria-hidden' => 'true']))
            ->classes(
                'flex items-center',
                TALLKit::gap(size: $size),
                match ($position) {
                    'top' => 'flex-col-reverse',
                    'left' => 'flex-row-reverse',
                    'right' => 'flex-row',
                    default => 'flex-col',
                },
                match ($overlay) {
                    'top' => 'absolute inset-x-0 top-0 z-10 pointer-events-none',
                    'bottom' => 'absolute inset-x-0 bottom-0 z-10 pointer-events-none',
                    'fill' => 'absolute inset-0 pointer-events-none',
                    default => null,
                },
            )
    }}
>
    <div
        @unless ($decorative)
            role="progressbar"
            aria-valuemin="0"
            aria-valuemax="100"
            aria-valuenow="{{ (int) ($value ?? 0) }}"
            x-bind:aria-valuenow="Math.round({{ $displayValue }})"
        @endunless
        {{
            $attributes
                ->whereDoesntStartWith(['container:', 'bar:', 'percent:'])
                ->classes([
                    '
                        w-full overflow-hidden
                        pointer-events-none
                    ',
                    'rounded-full' => ! $square,
                    'bg-zinc-200 dark:bg-white/10' => ! $overlay,
                    'h-full' => $overlay === 'fill',
                    TALLKit::generateClassBySize(size: $size, name: 'h', values: ['px', '0.5', '1', '1.5', '2', '2.5', '3']) => $overlay !== 'fill',
                ])
                ->merge(['aria-label' => $decorative ? null : __($label ?? 'Progress')])
        }}
    >
        <div
            {{
                $attributes->prefixed('bar:')
                    ->dataKey('progress-bar')
                    ->merge(['x-effect' => "const value = {$displayValue}; \$el.style.transition = value < Number(\$el.dataset.value ?? 0) ? 'none' : ''; \$el.style.width = value + '%'; \$el.dataset.value = value"])
                    ->classes([
                        'w-0 h-full transition-[width] ease-linear',
                        'rounded-full' => ! $square,
                        match ($variant) {
                            'accent' => 'bg-[var(--color-accent)]',
                            default => TALLKit::background(color: $color) ?? 'bg-zinc-800/95 dark:bg-white/95',
                        },
                    ])
            }}
        ></div>
    </div>

    @if ($position !== 'none')
        <tk:text
            :attributes="$attributes->prefixed('percent:')->dataKey('progress-percent')"
            :$size
            :label="($value ?? 0).'%'"
            x-text="Math.round({{ $displayValue }}) + '%'"
        />
    @endif
</div>
