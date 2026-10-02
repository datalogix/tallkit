@props([
    'type' => null,
])
<div wire:ignore.self {{
    $attributes
        ->dataKey('alert-progress')
        ->classes(
            '
                absolute m-0! pointer-events-none transition-[background-size] ease-linear
                bg-no-repeat bg-left bg-size-[100%_100%]
                bg-linear-to-r
            ',
            match ($type) {
                'top' => 'inset-x-0 top-0 h-1 from-current/40 to-current/40',
                'bottom' => 'inset-x-0 bottom-0 h-1 from-current/40 to-current/40',
                default => 'inset-0 from-black/5 to-black/5 dark:from-white/10 dark:to-white/10',
            },
        )
}}></div>
