@props([
    'exclusive' => null,
    'collapse' => null,
    'reversed' => null,
    'size' => null,
    'border' => null,
])
<div
    x-data="disclosureGroup({ exclusive: @js($exclusive) })"
    {{
        $attributes
            ->classes([
                'divide-y divide-zinc-300 dark:divide-zinc-700',
                'border-zinc-300 dark:border-zinc-700' => $border,
                TALLKit::roundedSize(size: $size, mode: 'small'),
                TALLKit::borderStyle(style: $border),
            ])
    }}
>
    {{ $slot }}
</div>
