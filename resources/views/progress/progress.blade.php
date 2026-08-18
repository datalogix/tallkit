@props([
    'value' => null,
    'variable' => null,
    'size' => null,
    'variant' => null,
    'position' => null,
])
<div
    x-data="progress(@js($value ?? 0))"
    {{
        TALLKit::attributesAfter($attributes, 'container:')
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
                    ->classes(
                        'w-0 h-full transition-[width] rounded-full ease-linear',
                        match ($variant) {
                            'accent' => 'bg-[var(--color-accent)]',
                            'red' => 'bg-red-600 dark:bg-red-700',
                            'orange' => 'bg-orange-600 dark:bg-orange-500',
                            'amber' => 'bg-amber-600 dark:bg-amber-700',
                            'yellow' => 'bg-yellow-600 dark:bg-yellow-700',
                            'lime' => 'bg-lime-600 dark:bg-lime-700',
                            'green' => 'bg-green-600 dark:bg-green-700',
                            'emerald' => 'bg-emerald-600 dark:bg-emerald-700',
                            'teal' => 'bg-teal-600 dark:bg-teal-700',
                            'cyan' => 'bg-cyan-600 dark:bg-cyan-700',
                            'sky' => 'bg-sky-600 dark:bg-sky-700',
                            'blue' => 'bg-blue-600 dark:bg-blue-700',
                            'indigo' => 'bg-indigo-600 dark:bg-indigo-700',
                            'violet' => 'bg-violet-600 dark:bg-violet-700',
                            'purple' => 'bg-purple-600 dark:bg-purple-700',
                            'fuchsia' => 'bg-fuchsia-600 dark:bg-fuchsia-700',
                            'pink' => 'bg-pink-600 dark:bg-pink-700',
                            'rose' => 'bg-rose-600 dark:bg-rose-700',
                            default => 'bg-zinc-800/95 dark:bg-white/95',
                        },
                    )
                    ->when(
                        $variable,
                        fn ($attrs) => $attrs->merge(['x-bind:style' => "{ width: $variable + '%' }"]),
                        fn ($attrs) => $attrs->merge(['x-bind:style' => "{ width: value + '%' }"])
                    )
            }}
        ></div>
    </div>

    @if ($position !== 'none')
        <tk:text
            :attributes="TALLKit::attributesAfter($attributes, 'percent:')->dataKey('progress-percent')"
            as="span"
            :$size
            :label="($value ?? 0).'%'"
            x-text="{{ $variable ?? 'value' }} + '%'"
        />
    @endif
</div>
