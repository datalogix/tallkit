@props([
    'index' => null,
    'size' => null,
    'status' => null,
    'icon-completed' => null,
    'icon-active' => null,
])
@php

$iconCompleted = ${'icon-completed'} ?? $attributes->pluck('iconCompleted');
$iconActive = ${'icon-active'} ?? $attributes->pluck('iconActive');

@endphp
<tk:element
    :attributes="$attributes->whereDoesntStartWith(['icon:', 'bullet:'])->classes(
        'flex flex-col items-center flex-1 text-center gap-2',
        TALLKit::fontSize(size: $size),
    )"
    :icon:class="TALLKit::classes(
        'rounded-full text-white flex items-center justify-center font-semibold shrink-0',
        TALLKit::widthHeight(size: $size, mode: 'large'),
        match ($status) {
            'completed' => 'bg-green-600',
            'active' => 'bg-blue-500',
            default => 'bg-gray-400',
        },
    )"
>
    <x-slot:icon>
        @if ($icon)
            <tk:icon
                :attributes="TALLKit::attributesAfter($attributes, 'icon:')"
                size="sm"
                :icon="match ($status) {
                    'completed' => $iconCompleted,
                    'active' => $iconActive,
                    default => $icon,
                } ?? $icon"
            />
        @elseif ($index)
            {{ $index }}
        @else
            <span {{ TALLKit::attributesAfter($attributes, 'bullet:')->classes(
                'bg-white rounded-full',
                TALLKit::widthHeight(size: $size, mode: 'smallest')
            ) }}></span>
        @endif
    </x-slot:icon>

    {{ $slot }}
</tk:element>
