@props([
    'size' => null,
    'variant' => null,
])
<div
    {{ $attributes->classes(TALLKit::spaceBlock(size: $size, mode: 'largest')) }}
    x-data="tab"
>
    {{ $slot }}
</div>
