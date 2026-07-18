@aware(['mode', 'variant'])
@props([
    'mode' => null,
    'variant' => null,
])
<div
    x-data="navIndicator({ mode: @js($mode) })"
    {{
        $attributes
            ->classes(
                '
                    absolute top-0 left-0 pointer-events-none
                    duration-700 ease-in-out will-change-transform
                    transition-[transform,width,height,border-radius,opacity,top,left]
                ',
                match ($mode) {
                    'bg' => 'rounded',
                    'line-left', 'line-right' => 'w-0.5',
                    'line-top', 'line-bottom' => 'h-0.5',
                    default => '',
                },
                match ($variant) {
                    'accent' => match ($mode) {
                        'bg' => 'bg-[color-mix(in_oklab,_var(--color-accent-content),_transparent_90%)]',
                        default => 'bg-(--color-accent-content)',
                    },
                    default => match ($mode) {
                        'bg' => 'bg-zinc-800/10 dark:bg-white/10',
                        default => 'bg-zinc-800 dark:bg-white',
                    },
                },
            )
    }}
></div>
