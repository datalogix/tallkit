@props([
    'size' => null,
    'variant' => null,
    'selectFirst' => null,
])
<div
    x-data="tab({ selectFirst: {{ $selectFirst !== false ? 'true' : 'false' }} })"
    x-modelable="selected"
    {{ $attributes->classes(TALLKit::spaceBlock(size: $size, mode: 'largest')) }}
>
    {{ $slot }}
</div>
