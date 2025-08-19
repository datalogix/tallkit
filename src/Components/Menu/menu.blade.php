<div
    x-data="menu"
    popover="manual"
    role="menu"
    {{
        $attributes->classes(
            '
            [:where(&)]:min-w-48 p-[.4rem]
            rounded-lg shadow-xs
            border border-zinc-300 dark:border-zinc-600
            bg-white dark:bg-zinc-700
            focus:outline-hidden
            '
        )->merge(['data-keep-open' => $keepOpen])
    }}
>
    {{ $slot }}
</div>
