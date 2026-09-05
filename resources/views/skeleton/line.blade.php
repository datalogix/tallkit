@props([
    'size' => null,
])
<tk:skeleton :attributes="$attributes->classes(TALLKit::height(size: $size, mode: 'large'))">
    {{ $slot }}
</tk:skeleton>
