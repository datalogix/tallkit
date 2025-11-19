<div
    popover="manual"
    {{
        $attributes->classes(
            '
            [:where(&)]:min-w-48 p-4
            rounded-lg shadow-xs
            border border-zinc-200 dark:border-white/10
            bg-white dark:bg-zinc-700
            text-zinc-800 dark:text-white
            '
        )->merge(['data-keep-open' => $keepOpen])
    }}
>
    {{ $slot }}
</div>
