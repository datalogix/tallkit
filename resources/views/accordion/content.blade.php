@aware(['size', 'collapse', 'border'])
@props([
    'size' => null,
    'collapse' => null,
    'border' => null,
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
            ->merge(['x-show' => 'opened'])
            ->merge(
                match (true) {
                    $collapse === false => [],
                    is_string($collapse) => ['x-collapse.'.$collapse => ''],
                    default => ['x-collapse' => ''],
                }
            )
    }}
>
    {{ $slot }}
</div>
