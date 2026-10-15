@props([
    'sticky' => null,
    'ariaLabel' => null,
])
<aside {{
    $attributes
        ->dataKey('aside')
        ->merge(['aria-label' => $ariaLabel ? __($ariaLabel) : null])
        ->classes(
            '
                [grid-area:aside]
                [:where(&)]:w-64 p-4
                border-l rtl:border-l-0 rtl:border-r border-zinc-300 dark:border-zinc-700
                max-lg:w-auto! max-lg:border-l-0 max-lg:rtl:border-r-0 max-lg:border-t
            '
        )
        ->when($sticky, fn ($attrs) => $attrs->classes('lg:overflow-y-auto lg:overscroll-contain max-lg:static! max-lg:max-h-none!'))
        ->mergeDefined(['x-data' => $sticky ? 'aside' : null, 'wire:ignore.self' => $sticky ? true : null])
}}>
    {{ $slot }}
</aside>
