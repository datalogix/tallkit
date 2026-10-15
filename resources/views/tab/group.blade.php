@props([
    'size' => null,
    'variant' => null,
    'selectFirst' => null,
    'orientation' => null,
])
<div
    wire:ignore.self
    x-data="tab({{ Js::from([
        'selectFirst' => $selectFirst !== false,
        'orientation' => $orientation === 'vertical' ? 'vertical' : 'horizontal',
    ]) }})"
    x-modelable="selected"
    x-cloak
    {{
        $attributes
            ->dataKey('tab-group')
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
