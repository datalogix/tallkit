<div
    {{
        $attributes
            ->whereDoesntStartWith(['indicator:'])
            ->dataKey('carousel-indicators')
            ->classes('flex items-center justify-center mt-2')
            ->merge(['aria-label' => __('Slides')])
    }}
    role="group"
>
    <template x-for="page in pageCount()" :key="page">
        <button
            {{
                $attributes->prefixed('indicator:')
                    ->classes(
                        '
                            size-6 flex items-center justify-center rounded-full
                            focus-visible:tk-focus-outline focus-visible:outline-offset-0
                            before:content-[\'\'] before:size-2 before:rounded-full before:transition-colors
                            before:bg-zinc-800/20 hover:before:bg-zinc-800/40
                            dark:before:bg-white/20 dark:hover:before:bg-white/40
                            [&[data-active]]:before:bg-[var(--color-accent)]
                        '
                    )
            }}
            type="button"
            x-on:click="goToPage(page - 1)"
            :aria-current="isPageActive(page - 1) ? 'true' : null"
            :aria-label="@js(__('Go to slide :number')).replace(':number', page)"
            :data-active="isPageActive(page - 1) ? '' : null"
        ></button>
    </template>
</div>
