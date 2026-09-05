@props([
    'kbd' => null,
    'size' => null,
    'variant' => null,
    'arrow' => null,
])
<div
    popover="manual"
    role="tooltip"
    {{
        $attributes->whereDoesntStartWith(['kbd:', 'arrow:'])->classes(
            'group relative overflow-visible text-white border border-white/10',
            match ($variant) {
                'accent' => 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)]',
                'inverse' => 'bg-white text-zinc-800 border-black/10',
                default => TALLKit::background(color: $variant) ?? 'bg-zinc-800 dark:bg-zinc-700',
            },
            TALLKit::fontSize(size: $size, weight: true, mode: 'small'),
            TALLKit::padding(size: $size, mode: 'smallest'),
            TALLKit::roundedSize(size: $size),
        )
    }}
>
    <div class="flex gap-1.5">
        <div class="flex-1">
            {{ $slot }}
        </div>

        @if (isset($kbd) && $kbd !== '')
            <tk:kbd
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'kbd:')->classes('ps-1')"
                :$size
                :label="$kbd"
                variant="text"
            />
        @endif
    </div>

    @if ($arrow)
        <tk:icon
            name="{{ is_string($arrow) ? $arrow : 'typcn:arrow-sorted-down' }}"
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'arrow:')->classes(
                '
                    absolute w-5 h-5 pointer-events-none

                    group-data-[position=top]:left-1/2
                    group-data-[position=top]:-translate-x-1/2
                    group-data-[position=top]:-bottom-3
                    group-data-[position=top]:rotate-0
                    group-data-[position=top]:mb-px

                    group-data-[position=bottom]:left-1/2
                    group-data-[position=bottom]:-translate-x-1/2
                    group-data-[position=bottom]:-top-3
                    group-data-[position=bottom]:rotate-180
                    group-data-[position=bottom]:mt-px

                    group-data-[position=left]:top-1/2
                    group-data-[position=left]:-translate-y-1/2
                    group-data-[position=left]:-right-3
                    group-data-[position=left]:rotate-270
                    group-data-[position=left]:mr-px

                    group-data-[position=right]:top-1/2
                    group-data-[position=right]:-translate-y-1/2
                    group-data-[position=right]:-left-3
                    group-data-[position=right]:rotate-90
                    group-data-[position=right]:ml-px
                ',
                match ($variant) {
                    'accent' => 'text-[var(--color-accent)]',
                    'inverse' => 'text-white',
                    default => TALLKit::textStrong(color: $variant) ?? 'text-zinc-800 dark:text-zinc-700',
                },
            )"
        />
    @endif
</div>
