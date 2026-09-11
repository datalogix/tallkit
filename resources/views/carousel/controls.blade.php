@props([
    'name' => null,
    'arrows' => null,
    'indicators' => null,
    'position' => null,
])
<div
    x-data="carouselControls({{ Js::from(['name' => $name]) }})"
    {{
        $attributes
            ->dataKey('carousel-controls')
            ->whereDoesntStartWith(['arrows:', 'prev:', 'next:', 'indicators:'])
            ->classes('flex flex-col items-center gap-2')
    }}
>
    @if ($arrows !== false)
        <tk:carousel.arrows
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'arrows:')"
            :$position
        />
    @endif

    @if ($indicators !== false)
        <tk:carousel.indicators
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'indicators:')"
        />
    @endif
</div>
