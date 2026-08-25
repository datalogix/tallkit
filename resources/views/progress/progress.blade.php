@props([
    'value' => null,
    'variable' => null,
    'size' => null,
    'variant' => null,
    'position' => null,
])
@php

$displayValue = $variable ? "Math.max(0, Math.min(100, Number({$variable}) || 0))" : 'value';

@endphp
<div
    {{
        TALLKit::attributesAfter($attributes, 'container:')
            ->unless($variable, fn ($attrs) => $attrs->merge(['x-data' => "progress(@js($value ?? 0))"]))
            ->classes(
                'flex items-center',
                TALLKit::gap(size: $size),
                match ($position) {
                    'top' => 'flex-col-reverse',
                    'left' => 'flex-row-reverse',
                    'right' => 'flex-row',
                    default => 'flex-col',
                }
            )
    }}
>
    <div
        role="progressbar"
        aria-valuemin="0"
        aria-valuemax="100"
        aria-valuenow="{{ (int) ($value ?? 0) }}"
        x-bind:aria-valuenow="Math.round({{ $displayValue }})"
        {{
            $attributes
                ->whereDoesntStartWith(['container:', 'bar:', 'percent:'])
                ->classes(
                    '
                        w-full overflow-hidden
                        pointer-events-none
                        bg-zinc-200 dark:bg-white/10
                        rounded-full
                    ',
                    TALLKit::generateClassBySize(size: $size, name: 'h', values: ['px', '0.5', '1', '1.5', '2', '2.5', '3']),
                )
        }}
    >
        <div
            {{
                TALLKit::attributesAfter($attributes, 'bar:')
                    ->dataKey('progress-bar')
                    ->merge(['x-bind:style' => "{ width: ({$displayValue}) + '%' }"])
                    ->classes(
                        'w-0 h-full transition-[width] rounded-full ease-linear',
                        match ($variant) {
                            'accent' => 'bg-[var(--color-accent)]',
                            default => TALLKit::background($variant) ?? 'bg-zinc-800/95 dark:bg-white/95',
                        },
                    )
            }}
        ></div>
    </div>

    @if ($position !== 'none')
        <tk:text
            :attributes="TALLKit::attributesAfter($attributes, 'percent:')->dataKey('progress-percent')"
            :$size
            :label="($value ?? 0).'%'"
            x-text="Math.round({{ $displayValue }}) + '%'"
        />
    @endif
</div>
