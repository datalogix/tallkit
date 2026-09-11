@aware(['fade'])
@props([
    'name' => null,
])
<div
    {{
        $attributes
            ->dataKey('carousel-slide')
            ->classes([
                'shrink-0',
                '[grid-area:1/1] opacity-0 first:opacity-100 transition-opacity duration-300 ease-in-out' => $fade,
            ])
            ->merge(in_livewire() ? ['wire:key' => $name ?? TALLKit::generateId(prefix: 'carousel-slide')] : [], false)
    }}
    role="group"
    aria-roledescription="slide"
    :aria-hidden="!isSlideVisible($el) ? 'true' : null"
    :inert="!isSlideVisible($el)"
>
    {{ $slot }}
</div>
