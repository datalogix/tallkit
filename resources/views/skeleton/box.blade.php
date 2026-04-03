@props([
    'size' => null,
])
<tk:skeleton :attributes="$attributes->classes(TALLKit::widthHeight($size, mode: 'large'))">
    {{ $slot }}
</tk:skeleton>
