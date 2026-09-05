@props([
    'size' => null,
])
<tk:skeleton :attributes="$attributes->classes(TALLKit::widthHeight(size:$size, mode: 'large'))">
    {{ $slot }}
</tk:skeleton>
