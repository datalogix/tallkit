@props([
    'exclusive' => null,
    'collapse' => null,
    'reversed' => null,
    'size' => null,
    'border' => null,
])
<div
    x-cloak
    x-data="disclosureGroup(@js($exclusive ?? false))"
    {{
        $attributes->dataKey('accordion')->classes([
            'divide-y divide-gray-400 dark:divide-gray-600',
            'border-gray-400 dark:border-gray-600' => $border,
            TALLKit::roundedSize(size: $size),
            TALLKit::borderStyle(style: $border),
        ])
    }}
>
    {{ $slot }}
</div>
