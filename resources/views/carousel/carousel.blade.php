@props([
    'name' => null,
    'arrows' => null,
    'arrowsPosition' => null,
    'indicators' => null,
    'autoplay' => null,
    'interval' => null,
    'fade' => null,
    'advance' => null,
    'wrap' => null,
])
<div
    wire:ignore.self
    x-data="carousel({{ Js::from([
        'name' => $name ?? TALLKit::generateId(prefix: 'carousel'),
        'autoplay' => (bool) $autoplay,
        'interval' => (int) ($interval ?: 5000),
        'advance' => $advance === 'page' ? 'page' : 'slide',
        'wrap' => $wrap !== false,
        'fade' => (bool) $fade,
    ]) }})"
    x-modelable="current"
    x-cloak
    role="region"
    aria-roledescription="carousel"
    {{
        TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:')
            ->dataKey('carousel')
    }}
>
    <div
        {{
            TALLKit::attributesAfter(attributes: $attributes, prefix: 'area:')
                ->classes(['relative' => $arrowsPosition !== 'outside'])
        }}
    >
        <div
            {{
                $attributes
                    ->whereDoesntStartWith(['container:', 'area:', 'track:', 'slide:', 'arrows:', 'prev:', 'next:', 'indicators:'])
                    ->dataKey('carousel-viewport')
                    ->classes('overflow-hidden')
            }}
        >
            <div
                {{
                    TALLKit::attributesAfter(attributes: $attributes, prefix: 'track:')
                        ->dataKey('carousel-track')
                        ->classes([
                            'grid' => $fade,
                            'flex transition-transform duration-300 ease-in-out will-change-transform' => ! $fade,
                        ])
                }}
            >
                {{ $slot }}
            </div>
        </div>

        @if ($arrows !== false && $arrowsPosition !== 'outside')
            <tk:carousel.arrows
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'arrows:')"
                :position="$arrowsPosition"
            />
        @endif
    </div>

    @if ($arrows !== false && $arrowsPosition === 'outside')
        <tk:carousel.arrows
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'arrows:')"
            :position="$arrowsPosition"
        />
    @endif

    @if ($indicators !== false)
        <tk:carousel.indicators
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'indicators:')"
        />
    @endif
</div>
