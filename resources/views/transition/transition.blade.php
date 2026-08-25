@props([
    'animation' => null,
])
<div
    {{ $attributes->dataKey('transition') }}
    @if ($animation === 'fade')
        x-transition:enter="transition-opacity duration-500 ease-linear"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300 ease-linear"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    @elseif ($animation === 'slide')
        x-transition:enter="transition-all duration-200 ease-out"
        x-transition:enter-start="opacity-0 group-data-[position=bottom]:-translate-y-1/2 group-data-[position=top]:translate-y-1/2 group-data-[position=left]:translate-x-1/2 group-data-[position=right]:-translate-x-1/2"
        x-transition:enter-end="opacity-100 translate-x-0 translate-y-0"
        x-transition:leave="transition-all duration-150 ease-in"
        x-transition:leave-start="opacity-100 translate-x-0 translate-y-0"
        x-transition:leave-end="opacity-0 group-data-[position=bottom]:-translate-y-1/2 group-data-[position=top]:translate-y-1/2 group-data-[position=left]:translate-x-1/2 group-data-[position=right]:-translate-x-1/2"
    @elseif ($animation === 'slide-up')
        x-transition:enter="transition-all duration-200 ease-out"
        x-transition:enter-start="opacity-0 translate-y-1/2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition-all duration-150 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1/2"
    @elseif ($animation === 'slide-down')
        x-transition:enter="transition-all duration-200 ease-out"
        x-transition:enter-start="opacity-0 -translate-y-1/2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition-all duration-150 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1/2"
    @elseif ($animation === 'slide-left')
        x-transition:enter="transition-all duration-200 ease-out"
        x-transition:enter-start="opacity-0 translate-x-1/2"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition-all duration-150 ease-in"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-1/2"
    @elseif ($animation === 'slide-right')
        x-transition:enter="transition-all duration-200 ease-out"
        x-transition:enter-start="opacity-0 -translate-x-1/2"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition-all duration-150 ease-in"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-1/2"
    @elseif ($animation === 'slide-up-full')
        x-transition:enter="transition-all duration-350 ease-out"
        x-transition:enter-start="opacity-0 translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition-all duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-full"
    @elseif ($animation === 'slide-down-full')
        x-transition:enter="transition-all duration-350 ease-out"
        x-transition:enter-start="opacity-0 -translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition-all duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-full"
    @elseif ($animation === 'slide-left-full')
        x-transition:enter="transition-all duration-350 ease-out"
        x-transition:enter-start="opacity-0 translate-x-full"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition-all duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-full"
    @elseif ($animation === 'slide-right-full')
        x-transition:enter="transition-all duration-350 ease-out"
        x-transition:enter-start="opacity-0 -translate-x-full"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition-all duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-full"
    @elseif ($animation === 'zoom')
        x-transition:enter="transition-all duration-200 ease-out origin-center!"
        x-transition:enter-start="opacity-0 scale-0"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition-all duration-150 ease-in"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-0"
    @elseif ($animation === 'none')
    @else
        x-transition:enter="transition-all duration-300 ease-in-out"
        x-transition:enter-start="opacity-0 scale-50"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition-all duration-150 ease-in"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-50"
    @endif
>
    {{ $slot }}
</div>
