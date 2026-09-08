@aware(['size', 'variant', 'orientation'])
@props([
    'size' => null,
    'variant' => null,
    'orientation' => null,
])
<div
    {{

        TALLKit::attributesAfter(attributes: $attributes, prefix: 'scrollable:')
            ->classes('overflow-x-auto overflow-y-hidden shrink-0')
    }}
>
    <div
        {{
            $attributes
                ->whereDoesntStartWith(['scrollable:'])
                ->merge(['aria-orientation' => $orientation === 'vertical' ? 'vertical' : 'horizontal'])
                ->classes(
                    TALLKit::gap(size: $size),
                    match ($variant) {
                        'line' => 'flex border-zinc-800/10 dark:border-white/20',
                        'pills' => 'flex ',
                        'segmented' => 'inline-flex p-1 rounded-lg bg-zinc-800/10 dark:bg-white/10',
                        default => 'flex border-zinc-800/10 dark:border-white/20'
                    },
                )
                ->when(
                    $orientation === 'vertical',
                    fn ($attrs) => $attrs->classes(
                        'flex-col',
                        match ($variant) {
                            'line' => 'border-r',
                            'pills' => 'w-full',
                            'segmented' => '',
                            default => 'border-r'
                        },
                    ),
                    fn ($attrs) => $attrs->classes(
                        TALLKit::height(size: $size),
                        match ($variant) {
                            'line' => 'border-b',
                            'pills' => 'w-full',
                            'segmented' => '',
                            default => 'border-b'
                        },
                    ),
                )
        }}
        role="tablist"
    >
        {{ $slot }}
    </div>
</div>
