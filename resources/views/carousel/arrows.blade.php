@props([
    'position' => null,
])
<div
    {{
        $attributes
            ->dataKey('carousel-arrows')
            ->whereDoesntStartWith(['prev:', 'next:'])
            ->classes(match ($position) {
                'overlap' => 'absolute inset-y-0 -left-4 -right-4 flex items-center justify-between z-10 pointer-events-none',
                'outside' => 'flex items-center justify-between mt-3',
                default => 'absolute inset-0 flex items-center justify-between px-2 pointer-events-none z-10'
            })
    }}
>
    <tk:button
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'prev:')
            ->classes(match ($position) {
                'overlap' => 'pointer-events-auto',
                'outside' => '',
                default => 'pointer-events-auto bg-white/80 dark:bg-zinc-800/80 backdrop-blur'
            }, 'rounded-full')
        "
        variant="subtle"
        icon="chevron-left"
        tooltip="Previous slide"
        ::disabled="isFirst()"
        @click="prev()"
    />

    <tk:button
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'next:')
            ->classes(match ($position) {
                'overlap' => 'pointer-events-auto',
                'outside' => '',
                default => 'pointer-events-auto bg-white/80 dark:bg-zinc-800/80 backdrop-blur'
            }, 'rounded-full')
        "
        variant="subtle"
        icon="chevron-right"
        tooltip="Next slide"
        ::disabled="isLast()"
        @click="next()"
    />
</div>
