@aware(['size', 'variant'])
@props([
    'size' => null,
    'variant' => null,
])
<div
    {{

        TALLKit::attributesAfter($attributes, 'scrollable:')
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
                        'pills' => 'flex w-full',
                        'segmented' => 'inline-flex p-1 rounded-lg bg-zinc-800/10 dark:bg-white/10',
                        default => 'flex border-b border-zinc-800/10 dark:border-white/20'
                    },
                )
        }}
        role="tablist"
        x-modelable="selected"
    >
        {{ $slot }}
    </div>
</div>
