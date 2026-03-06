<div
    popover="manual"
    {{
        $attributes->classes(
            '
                [:where(&)]:min-w-48 shadow-xs
                border border-zinc-200 dark:border-white/10
                bg-white dark:bg-zinc-700
            ',
            $textColor(variant: $variant, contrast: 'strong'),
            $padding(size: $size, mode: 'small'),
            $roundedSize($size, mode: 'large'),
        )->merge(['data-keep-open' => $keepOpen])
    }}
>
    {{ $slot }}
</div>
