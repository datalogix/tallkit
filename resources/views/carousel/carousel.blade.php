@props([
    'name' => null,
    'slides' => null,
    'color' => null,
    'perView' => null,
    'gap' => null,
    'thumbnails' => null,
    'progress' => null,
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
@php

$slides = collect($slides)->map(fn ($slide) => is_array($slide) ? $slide : ['src' => $slide]);
$gap = is_numeric($gap) ? 'calc(var(--spacing) * '.$gap.')' : $gap;
$indicators ??= $thumbnails ? false : null;
$hasSlotSlides = $slot->hasActualContent();
$progressOverlay = $autoplay && $progress === 'overlay';
$progressAttributes = $attributes->prefixed('progress:')
    ->when($progressOverlay, fn ($attrs) => $attrs->classes('bg-black/10 dark:bg-black/10'))
    ->merge(['container:x-show' => 'autoplays() && canNavigate()']);

@endphp
@if ($hasSlotSlides || $slides->isNotEmpty())
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
                ->classes('flex flex-col gap-2')
                ->merge(['aria-label' => $label ? __($label) : null, 'aria-roledescription' => __('carousel')])
        }}
    >
        <div
            {{
                $attributes->prefixed('area:')
                    ->classes(['relative' => $arrowsPosition !== 'outside' || $progressOverlay])
            }}
        >
            <div
                {{
                    $attributes
                        ->whereDoesntStartWith(['container:', 'area:', 'track:', 'slide:', 'arrows:', 'prev:', 'next:', 'indicators:', 'thumbnails:', 'progress:', 'pause:', 'pause-area:'])
                        ->dataKey('carousel-viewport')
                        ->classes('overflow-hidden touch-pan-y')
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
                                'gap-(--tk-carousel-gap)' => $gap && ! $fade,
                            ])
                            ->mergeDefined([
                                'style' => collect([
                                    '--tk-carousel-per-view' => $perView && ! $fade ? max(1, (int) $perView) : null,
                                    '--tk-carousel-gap' => $gap && ! $fade ? $gap : null,
                                ])->filter()->map(fn ($value, $key) => $key.': '.$value)->implode('; ') ?: null,
                            ])
                    }}
                    x-bind:aria-live="isRotating() ? 'off' : 'polite'"
                >
                    {{ $slot }}

                    @foreach ($slides as $slide)
                        <tk:carousel.slide
                            :attributes="$attributes->prefixed('slide:')
                                ->except('position')
                                ->merge(TALLKit::attributesFromItem(Arr::except($slide, ['name', 'src', 'alt', 'content', 'position', 'thumbnail'])), false)
                                ->mergeDefined([
                                    'image:loading' => $loop->first && ! $hasSlotSlides ? null : 'lazy',
                                    'image:fetchpriority' => $loop->first && ! $hasSlotSlides ? 'high' : null,
                                ])
                            "
                            :name="$slide['name'] ?? null"
                            :src="$slide['src'] ?? null"
                            :alt="$slide['alt'] ?? null"
                            :content="$slide['content'] ?? null"
                            :position="$slide['position'] ?? $attributes->get('slide:position')"
                            :thumbnail="$slide['thumbnail'] ?? null"
                        />
                    @endforeach
                </div>
            </div>

            @if ($progressOverlay)
                <tk:progress
                    :attributes="$progressAttributes"
                    variable="progress() * 100"
                    :$color
                    :variant="TALLKit::isColor($color) ? null : 'accent'"
                    overlay="bottom"
                />
            @endif

            @if ($arrows !== false && $arrowsPosition !== 'outside')
                <tk:carousel.arrows
                    :attributes="$attributes->prefixed('arrows:')"
                    :position="$arrowsPosition"
                />
            @endif
        </div>

        @if ($autoplay && $progress && ! $progressOverlay)
            <tk:progress
                :attributes="$progressAttributes"
                variable="progress() * 100"
                :$color
                :variant="TALLKit::isColor($color) ? null : 'accent'"
                :label="false"
                position="none"
            />
        @endif

        @if ($arrows !== false && $arrowsPosition === 'outside')
            <tk:carousel.arrows
                :attributes="$attributes->prefixed('arrows:')"
                :position="$arrowsPosition"
            />
        @endif

        @if ($thumbnails)
            <tk:carousel.thumbnails
                :attributes="$attributes->prefixed('thumbnails:')"
                :$color
            />
        @endif

        @if ($indicators !== false)
            <tk:carousel.indicators
                :attributes="$attributes->prefixed('indicators:')"
                :$color
                :variant="$indicators === 'counter' ? 'counter' : null"
            />
        @endif

        @if ($autoplay)
            <div
                x-show="autoplays()"
                {{ $attributes->prefixed('pause-area:')->classes('flex justify-center') }}
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
@endif
