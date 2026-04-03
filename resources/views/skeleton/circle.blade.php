@props([
    'size' => null,
])
<tk:skeleton :attributes="$attributes->classes(
    TALLKit::widthHeight($size, mode: 'large'),
    '[:where(&)]:rounded-full',
)">
    {{ $slot }}
</tk:skeleton>
