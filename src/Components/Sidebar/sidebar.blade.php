@if ($stashable)
    <tk:sidebar.backdrop
        :attributes="$attributesAfter('backdrop:')"
        :$name
    />
@endif

<div
    x-data="sidebar(@js($name), @js($sticky), @js($stashable))"
    data-mobile-cloak
    {{
        $attributes
            ->whereDoesntStartWith(['backdrop:'])
            ->classes('
                [grid-area:sidebar]
                z-1 flex flex-col gap-4
                [:where(&)]:w-64 p-4
                [:where(&)]:bg-zinc-50 dark:[:where(&)]:bg-zinc-800
                border-r rtl:border-r-0 rtl:border-l border-zinc-300 dark:border-zinc-700
            ')
            ->when($sticky, fn ($attrs) => $attrs->classes('max-h-dvh overflow-y-auto overscroll-contain'))
            ->when($stashable, fn ($attrs) => $attrs->classes(
                'max-lg:data-mobile-cloak:hidden',
                'data-show-stashed-sidebar:translate-x-0! lg:translate-x-0!',
                'z-20! data-stashed:start-0! data-stashed:fixed! data-stashed:top-0! data-stashed:min-h-dvh! data-stashed:max-h-dvh!',
                '-translate-x-full rtl:translate-x-full transition-transform',
            ))
    }}
>
    {{ $slot }}
</div>
