@aware(['size', 'variant'])
@props(['size', 'variant'])

<tk:button
    :attributes="$attributes
        ->classes(
            'px-2 shrink-0',
             match($variant) {
                'pills' => '
                    rounded-full
                    bg-zinc-800/10 dark:bg-white/5
                    [&[data-selected]]:bg-zinc-800 dark:[&[data-selected]]:bg-white
                    [&[data-selected]]:text-white dark:[&[data-selected]]:text-zinc-800
                ',
                'segmented' => '
                    rounded-md
                    [&[data-selected]]:bg-white dark:[&[data-selected]]:bg-white/20
                ',
                default => '
                    -mb-px
                    border-b-2 border-transparent
                    [&[data-selected]]:border-zinc-800
                    dark:[&[data-selected]]:border-white
                '
            }
        )
        ->merge(['data-selected' => $selected ? '' : false])
        ->merge($name ? [
            'wire:key' => $name,
            'data-name' => $name,
            'role' => 'tab',
            ':tabindex' => 'isSelected(\'' . $name . '\') ? 0 : -1',
            ':aria-selected' => 'isSelected(\'' . $name . '\')',
            ':data-selected' => 'isSelected(\'' . $name . '\')',
            ':data-active' => 'isSelected(\'' . $name . '\')',
            '@click' => 'select(\'' . $name . '\')',
        ] : [])
    "
    :$size
    variant="none"
>
    {{ $slot }}
</tk:button>
