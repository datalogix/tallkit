@aware(['fade', 'arrows', 'arrowsPosition'])
@props([
    'name' => null,
    'src' => null,
    'alt' => null,
    'content' => null,
    'position' => null,
    'thumbnail' => null,
])
@php

$hasContent = $slot->hasActualContent() || filled($content);
$position = in_array($position, ['top', 'bottom', 'above', 'below'], true) ? $position : 'center';
$stacked = $src && $hasContent && in_array($position, ['above', 'below'], true);
$overlay = $src && $hasContent && ! $stacked;
$arrowsInside = $arrows !== false && ! in_array($arrowsPosition, ['outside', 'overlap'], true);

@endphp
<div
    wire:ignore.self
    {{
        $attributes
            ->whereDoesntStartWith(['image:', 'content:'])
            ->dataKey('carousel-slide')
            ->classes([
                '
                    mx-auto shrink-0 [:where(&)]:min-w-0
                    [:where(&)]:basis-[calc((100%_-_(var(--tk-carousel-per-view,1)_-_1)_*_var(--tk-carousel-gap,0px))_/_var(--tk-carousel-per-view,1))]
                ',
                'text-center' => ! $src && filled($content),
                'relative' => $overlay,
                'flex flex-col' => $stacked,
                'px-14' => ! $src && filled($content) && $arrowsInside,
                '[grid-area:1/1] opacity-0 first:opacity-100 transition-opacity duration-300 ease-in-out' => $fade,
            ])
            ->wireKey($name ?? TALLKit::stableId('carousel-slide'))
            ->merge(['aria-roledescription' => __('slide')])
            ->mergeDefined(['data-thumbnail' => $thumbnail])
    }}
    role="group"
    :aria-label="{{ Js::from(__('Slide :n of :total')) }}.replace(':n', slideNumber($el)).replace(':total', slideCount)"
    :aria-hidden="!isSlideVisible($el) ? 'true' : null"
    :inert="!isSlideVisible($el)"
>
    @if ($src)
        <img
            {{
                $attributes->prefixed('image:')
                    ->classes([
                        'block w-full object-cover',
                        'flex-1 min-h-0' => $stacked,
                        'h-full' => ! $stacked,
                    ])
                    ->merge(['src' => $src, 'alt' => __($alt ?? '')])
            }}
        />
    @endif

    @if ($src && $hasContent)
        <div
            {{
                $attributes->prefixed('content:')
                    ->dataKey('carousel-slide-content')
                    ->classes(
                        'flex flex-col items-center gap-2 text-center',
                        match ($position) {
                            'top' => 'absolute inset-0 p-6 justify-start text-white bg-linear-to-b from-black/60 via-transparent to-transparent',
                            'bottom' => 'absolute inset-0 p-6 justify-end text-white bg-linear-to-t from-black/60 via-transparent to-transparent',
                            'above' => 'order-first p-4',
                            'below' => 'p-4',
                            default => 'absolute inset-0 p-6 justify-center text-white bg-black/40',
                        },
                        ['px-14' => $overlay && $arrowsInside],
                    )
            }}
        >
            {{ is_string($content) ? __($content) : $content }}

            {{ $slot }}
        </div>
    @else
        {{ is_string($content) ? __($content) : $content }}

        {{ $slot }}
    @endif
</div>
