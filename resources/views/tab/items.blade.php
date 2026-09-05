@aware(['size', 'variant', 'orientation'])
@props([
    'size' => null,
    'variant' => null,
    'orientation' => null,
])
<div
    {{

        TALLKit::attributesAfter(attributes: $attributes, prefix: 'scrollable:')
            ->classes('overflow-x-auto overflow-y-hidden')
    }}
>
    <div
        {{
            $attributes
                ->whereDoesntStartWith(['scrollable:'])
                ->classes(
                    TALLKit::gap(size: $size),
                    TALLKit::height(size: $size),
                    match ($variant) {
                        'line' => 'flex border-b border-zinc-800/10 dark:border-white/20',
                        'pills' => 'flex w-full',
                        'segmented' => 'inline-flex p-1 rounded-lg bg-zinc-800/10 dark:bg-white/10',
                        default => 'flex border-b border-zinc-800/10 dark:border-white/20'
                    },
                )
                ->merge(['aria-orientation' => $orientation === 'vertical' ? 'vertical' : 'horizontal'])
        }}
        role="tablist"
    >
        {{ $slot }}
    </div>
</div>
