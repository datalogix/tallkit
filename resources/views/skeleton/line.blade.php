@props([
    'size' => null,
])
<tk:skeleton :attributes="$attributes->classes(TALLKit::height($size, mode: 'large'))">
    {{ $slot }}
</tk:skeleton>
