@props([
    'exclusive' => null,
    'collapse' => null,
    'reversed' => null,
    'size' => null,
    'border' => null,
    'disabled' => null,
    'level' => 2,
    'region' => null,
])
<div
    x-data="disclosureGroup({{ Js::from(['exclusive' => (bool) $exclusive]) }})"
    x-modelable="value"
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
