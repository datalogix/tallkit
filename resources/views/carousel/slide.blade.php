@aware(['fade'])
@props([
    'name' => null,
])
<div
    wire:ignore.self
    {{
        $attributes
            ->dataKey('carousel-slide')
            ->classes([
                'shrink-0 [:where(&)]:basis-full [:where(&)]:min-w-0',
                '[grid-area:1/1] opacity-0 first:opacity-100 transition-opacity duration-300 ease-in-out' => $fade,
            ])
            ->wireKey($name ?? TALLKit::stableId('carousel-slide'))
            ->merge(['aria-roledescription' => __('slide')])
    }}
    role="group"
    :aria-label="{{ Js::from(__('Slide :n of :total')) }}.replace(':n', slideNumber($el)).replace(':total', slides().length)"
    :aria-hidden="!isSlideVisible($el) ? 'true' : null"
    :inert="!isSlideVisible($el)"
>
    {{ $slot }}
</div>
