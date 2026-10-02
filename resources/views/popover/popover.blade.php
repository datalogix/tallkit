@aware(['animation'])
@props([
    'size' => null,
    'keepOpen' => null,
    'animation' => null,
])
<div
    wire:ignore.self
    popover="manual"
    {{
        $attributes->prefixed('container:')
            ->classes('group [:where(&)]:focus:outline-hidden bg-transparent')
            ->merge(['data-keep-open' => $keepOpen])
            ->wireKey(TALLKit::stableId('popover'))
    }}
>
    <tk:transition
        x-show="typeof opened === 'undefined' || opened"
        :$animation
        :attributes="
            $attributes
                ->whereDoesntStartWith(['container:'])
                ->classes(
                    '
                        [:where(&)]:bg-white dark:[:where(&)]:bg-zinc-700
                        [:where(&)]:text-zinc-700 dark:[:where(&)]:text-white
                        [:where(&)]:border [:where(&)]:border-zinc-200 dark:[:where(&)]:border-white/10
                        [:where(&)]:shadow-xs
                        [:where(&)]:overflow-auto

                        group-data-[position=bottom]:group-data-[align=left]:origin-top-left
                        group-data-[position=bottom]:group-data-[align=right]:origin-top-right
                        group-data-[position=bottom]:group-data-[align=center]:origin-top
                        group-data-[position=top]:group-data-[align=left]:origin-bottom-left
                        group-data-[position=top]:group-data-[align=right]:origin-bottom-right
                        group-data-[position=top]:group-data-[align=center]:origin-bottom
                        group-data-[position=right]:group-data-[align=left]:origin-top-left
                        group-data-[position=right]:group-data-[align=right]:origin-bottom-left
                        group-data-[position=right]:group-data-[align=center]:origin-left
                        group-data-[position=left]:group-data-[align=left]:origin-top-right
                        group-data-[position=left]:group-data-[align=right]:origin-bottom-right
                        group-data-[position=left]:group-data-[align=center]:origin-right
                    ',
                    TALLKit::fontSize(size: $size),
                    TALLKit::padding(size: $size, mode: 'smallest'),
                    TALLKit::roundedSize(size: $size, mode: 'large'),
                    TALLKit::generateClassBySize(size: $size, name: 'min-w', values: ['32', '40', '48', '56', '64', '72', '80']),
                    '[:where(&)]:max-w-[calc(100vw-1rem)]',
                    TALLKit::generateClassBySize(size: $size, name: 'max-h', values: ['48', '56', '64', '72', '80', '88', '96']),
                )
        "
    >
        {{ $slot }}
    </tk:transition>
</div>
