@aware(['size', 'collapse', 'reversed', 'border'])
@props([
    'size' => null,
    'collapse' => null,
    'reversed' => null,
    'border' => null,
    'expanded' => null,
    'disabled' => null,
    'heading' => null,
])
<div
    x-data="disclosure"
    {{
        $attributes
            ->dataKey('disclosure-item')
            ->whereDoesntStartWith(['heading:', 'content:'])
            ->classes('group/disclosure')
            ->merge(['data-open' => $expanded])
    }}
>
    <tk:button
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'heading:')->classes(
            TALLKit::paddingInline(size: $border ? $size : 'none', mode: 'largest'),
            TALLKit::paddingBlock(size: $size, mode: 'largest'),
            'w-full [&_[data-tallkit-icon]]:ml-auto',
        )"
        :$size
        :$disabled
        :label="$heading"
        variant="none"
        content:class="flex-1 justify-start"
        :icon="$reversed ? 'chevron-right' : false"
        icon::class="{ 'transition': {{ $collapse !== false ? 'true' : 'false' }}, 'rotate-90': opened }"
        :iconTrailing="$reversed ? false : 'chevron-down'"
        icon-trailing::class="{ 'transition': {{ $collapse !== false ? 'true' : 'false' }}, 'rotate-180': opened }"
    />

    <div
        x-cloak
        {{
            TALLKit::attributesAfter(attributes: $attributes, prefix: 'content:')
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
</div>
