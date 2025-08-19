<div
    popover="manual"
    {{
        $attributes->classes(
            '
            [:where(&)]:min-w-48 p-4
            rounded-lg shadow-xs
            border border-zinc-300 dark:border-zinc-600
            bg-white dark:bg-zinc-700
            '
        )->merge(['data-keep-open' => $keepOpen])
    }}
>
    {{ $slot }}
</div>
