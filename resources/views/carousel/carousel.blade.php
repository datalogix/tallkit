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
    'label' => null,
    'pauseLabel' => 'Pause slides',
    'playLabel' => 'Play slides',
])
<div
    wire:ignore.self
    x-data="carousel(@js([
        'name' => $name ?? TALLKit::stableId('carousel'),
        'autoplay' => (bool) $autoplay,
        'interval' => (int) ($interval ?: 5000),
        'advance' => $advance === 'page' ? 'page' : 'slide',
        'wrap' => $wrap !== false,
        'fade' => (bool) $fade,
    ]))"
    x-modelable="current"
    x-cloak
    role="{{ $label ? 'region' : 'group' }}"
    {{
        $attributes->prefixed('container:')
            ->dataKey('carousel')
            ->merge(['aria-label' => $label ? __($label) : null, 'aria-roledescription' => __('carousel')])
    }}
>
    <div
        {{
            $attributes->prefixed('area:')
                ->classes(['relative' => $arrowsPosition !== 'outside'])
        }}
    >
        <div
            {{
                $attributes
                    ->whereDoesntStartWith(['container:', 'area:', 'track:', 'slide:', 'arrows:', 'prev:', 'next:', 'indicators:', 'pause:', 'pause-area:'])
                    ->dataKey('carousel-viewport')
                    ->classes('overflow-hidden')
            }}
        >
            <div
                wire:ignore.self
                {{
                    $attributes->prefixed('track:')
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
                :attributes="$attributes->prefixed('arrows:')"
                :position="$arrowsPosition"
            />
        @endif
    </div>

    @if ($arrows !== false && $arrowsPosition === 'outside')
        <tk:carousel.arrows
            :attributes="$attributes->prefixed('arrows:')"
            :position="$arrowsPosition"
        />
    @endif

    @if ($indicators !== false)
        <tk:carousel.indicators
            :attributes="$attributes->prefixed('indicators:')"
        />
    @endif

    @if ($autoplay)
        <div
            x-show="autoplays()"
            {{ $attributes->prefixed('pause-area:')->classes('flex justify-center mt-2') }}
        >
            <tk:button
                :attributes="$attributes->prefixed('pause:')"
                size="sm"
                variant="subtle"
                :aria-label="__($pauseLabel)"
                ::aria-label="isPaused() ? {{ Js::from(__($playLabel)) }} : {{ Js::from(__($pauseLabel)) }}"
                x-on:click="togglePause()"
            >
                <tk:icon icon="pause" x-show="!isPaused()" />
                <tk:icon icon="play" x-show="isPaused()" x-cloak />
            </tk:button>
        </div>
    @endif
</div>
