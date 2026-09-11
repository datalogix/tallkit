<div
    {{
        $attributes
            ->whereDoesntStartWith(['indicator:'])
            ->dataKey('carousel-indicators')
            ->classes('flex items-center justify-center gap-1.5 mt-3')
    }}
    role="tablist"
    aria-label="{{ __('Slides') }}"
>
    <template x-for="page in pageCount()" :key="page">
        <button
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'indicator:')
                    ->classes(
                        '
                            size-2 rounded-full transition-colors
                            bg-zinc-800/20 hover:bg-zinc-800/40
                            dark:bg-white/20 dark:hover:bg-white/40
                            [&[data-active]]:bg-[var(--color-accent)]
                        '
                    )
            }}
            type="button"
            role="tab"
            x-on:click="goToPage(page - 1)"
            :aria-selected="isPageActive(page - 1) ? 'true' : 'false'"
            :aria-label="'Go to slide ' + page"
            :data-active="isPageActive(page - 1) ? '' : null"
        ></button>
    </template>
</div>
