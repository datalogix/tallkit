<tk:skeleton :attributes="$attributes->classes(
    $widthHeight($size, mode: 'large'),
    '[:where(&)]:rounded-full',
)">
    {{ $slot }}
</tk:skeleton>
