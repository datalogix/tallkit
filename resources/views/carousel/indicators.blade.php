@props([
    'color' => null,
    'variant' => null,
])
<div
    {{
        $attributes
            ->whereDoesntStartWith(['indicator:', 'counter:'])
            ->dataKey('carousel-indicators')
            ->classes('flex items-center justify-center')
            ->merge(['aria-label' => __('Slides')])
    }}
    role="group"
    x-show="canNavigate()"
>
    @if ($variant === 'counter')
        <span
            {{
                $attributes->prefixed('counter:')
                    ->classes('tabular-nums', TALLKit::fontSize(size: 'sm'), TALLKit::textNeutral(variant: 'muted'))
            }}
            x-text="`${currentPage() + 1} / ${pageCount()}`"
        ></span>
    @else
        <template x-for="page in pageCount()" :key="page">
            <button
                {{
                    $attributes->prefixed('indicator:')
                        ->classes([
                            '
                                size-6 flex items-center justify-center rounded-full
                                focus-visible:tk-focus-outline focus-visible:outline-offset-0
                                before:content-[\'\'] before:size-2 before:rounded-full before:transition-colors
                                before:bg-zinc-800/20 hover:before:bg-zinc-800/40
                                dark:before:bg-white/20 dark:hover:before:bg-white/40
                            ',
                            'tk-color-'.$color.' [&[data-active]]:before:bg-(--tk-fill)' => TALLKit::isColor($color),
                            '[&[data-active]]:before:bg-[var(--color-accent)]' => ! TALLKit::isColor($color),
                        ])
                }}
                type="button"
                x-on:click="goToPage(page - 1)"
                :aria-current="isPageActive(page - 1) ? 'true' : null"
                :aria-label="@js(__('Go to slide :number')).replace(':number', page)"
                :data-active="isPageActive(page - 1) ? '' : null"
            ></button>
        </template>
    @endif
</div>
