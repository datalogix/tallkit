@props([
    'size' => null,
    'variant' => null,
    'selectFirst' => null,
    'orientation' => null,
])
<div
    wire:ignore.self
    x-data="tab({
        selectFirst: {{ $selectFirst !== false ? 'true' : 'false' }},
        orientation: @js($orientation === 'vertical' ? 'vertical' : 'horizontal')
    })"
    x-modelable="selected"
    x-cloak
    {{
        $attributes
            ->classes([
                'flex flex-col',
                'flex-row' => $orientation === 'vertical',
                TALLKit::spaceBlock(size: $size, mode: 'large'),
                TALLKit::spaceInline(size: $size, mode: 'large'),
            ])
    }}
>
    {{ $slot }}
</div>
