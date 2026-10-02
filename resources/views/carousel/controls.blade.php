@props([
    'name' => null,
    'arrows' => null,
    'indicators' => null,
    'position' => null,
])
<div
    x-data="carouselControls(@js(['name' => $name]))"
    {{
        $attributes
            ->dataKey('carousel-controls')
            ->whereDoesntStartWith(['arrows:', 'prev:', 'next:', 'indicators:'])
            ->classes('flex flex-col items-center gap-2')
    }}
>
    @if ($arrows !== false)
        <tk:carousel.arrows
            :attributes="$attributes->prefixed('arrows:')"
            :$position
        />
    @endif

    @if ($indicators !== false)
        <tk:carousel.indicators
            :attributes="$attributes->prefixed('indicators:')"
        />
    @endif
</div>
