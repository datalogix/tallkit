@props([
    'color' => null,
])
<div
    {{
        $attributes
            ->whereDoesntStartWith(['thumbnail:'])
            ->dataKey('carousel-thumbnails')
            ->classes('flex gap-2 -my-1 p-1 overflow-x-auto overscroll-x-contain [scrollbar-width:none]')
            ->merge(['aria-label' => __('Slides')])
    }}
    role="group"
    x-show="canNavigate()"
>
    <template x-for="(slide, index) in slides()" :key="index">
        <button
            {{
                $attributes->prefixed('thumbnail:')
                    ->classes([
                        '
                            shrink-0 size-16 overflow-hidden rounded-md
                            flex items-center justify-center tabular-nums text-sm
                            bg-zinc-800/5 dark:bg-white/10
                            opacity-60 hover:opacity-100 transition-opacity
                            focus-visible:tk-focus-outline
                            [&[data-active]]:opacity-100 [&[data-active]]:ring-2
                        ',
                        'tk-color-'.$color.' [&[data-active]]:ring-(--tk-fill)' => TALLKit::isColor($color),
                        '[&[data-active]]:ring-[var(--color-accent)]' => ! TALLKit::isColor($color),
                    ])
            }}
            type="button"
            x-on:click="goTo(index)"
            :aria-current="index === current ? 'true' : null"
            :aria-label="@js(__('Go to slide :number')).replace(':number', index + 1)"
            :data-active="isIndexVisible(index) ? '' : null"
        >
            <template x-if="thumbnailOf(slide)">
                <img :src="thumbnailOf(slide)" alt="" class="size-full object-cover" loading="lazy" />
            </template>

            <template x-if="!thumbnailOf(slide)">
                <span x-text="index + 1"></span>
            </template>
        </button>
    </template>
</div>
