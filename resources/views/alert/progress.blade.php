@props([
    'type' => null,
    'size' => null,
])
<div {{
    $attributes
        ->dataKey('alert-progress')
        ->classes(
            '
                absolute pointer-events-none transition-[background-size] ease-linear
                w-full
                bg-no-repeat bg-left bg-size-[100%-100%]
                bg-linear-to-r
                from-black/5 to-black/5
                dark:from-white/10 dark:to-white/10
            ',
            match ($type) {
                'top','bottom' => TALLKit::height(size: $size, mode: 'smallest'),
                default => 'h-full',
            },
            match ($type) {
                'bottom' => 'translate-y-1/2 left-0 -bottom-1',
                default => 'left-0 -top-1',
            }
        )
}}></div>
