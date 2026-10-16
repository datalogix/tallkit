@aware(['size', 'collapse', 'border', 'region'])
@props([
    'size' => null,
    'collapse' => null,
    'border' => null,
    'region' => null,
])
<div
    x-cloak
    {{
        $attributes
            ->classes(
                TALLKit::fontSize(size: $size),
                TALLKit::paddingInline(size: $border ? $size : 'none', mode: 'largest'),
                TALLKit::paddingBlock(size: $size, mode: 'largest'),
                'pt-0!',
            )
            ->when($region !== false, fn ($attrs) => $attrs->merge(['role' => 'region']))
            ->merge([
                'x-show' => 'opened',
                ...match (true) {
                    $collapse === false => [],
                    is_string($collapse) => ['x-collapse.'.$collapse => ''],
                    default => ['x-collapse' => ''],
                },
            ])
    }}
>
    {{ $slot }}
</div>
