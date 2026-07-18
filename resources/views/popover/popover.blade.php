@props([
    'size' => null,
    'keepOpen' => null,
])
<div
    popover="manual"
    {{
        $attributes->classes(
            '
                [:where(&)]:bg-white dark:[:where(&)]:bg-zinc-700
                [:where(&)]:text-zinc-700 dark:[:where(&)]:text-white
                [:where(&)]:border [:where(&)]:border-zinc-200 [:where(&)]:dark:border-white/10
                [:where(&)]:shadow-xs [:where(&)]:focus:outline-hidden
                [:where(&)]:overflow-auto
            ',
            TALLKit::fontSize(size: $size),
            TALLKit::padding(size: $size, mode: 'smallest'),
            TALLKit::roundedSize(size: $size, mode: 'large'),
            TALLKit::generateClassBySize(size: $size, name: 'min-w', values: ['32', '40', '48', '56', '64', '72', '80']),
            TALLKit::generateClassBySize(size: $size, name: 'max-h', values: ['48', '56', '64', '72', '80', '88', '96']),
        )->merge(['data-keep-open' => $keepOpen])
    }}
>
    {{ $slot }}
</div>
