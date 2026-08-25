@props([
    'size' => null,
    'variant' => null,
    'selectFirst' => null,
    'orientation' => null,
])
<div
    {{ $attributes->classes(TALLKit::spaceBlock(size: $size, mode: 'largest')) }}
    x-data="tab({ selectFirst: {{ $selectFirst !== false ? 'true' : 'false' }}, orientation: @js($orientation ?? 'horizontal') })"
    x-modelable="selected"
    x-cloak
>
    {{ $slot }}
</div>
