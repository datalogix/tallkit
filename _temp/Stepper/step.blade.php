<tk:element
    :attributes="$attributes->whereDoesntStartWith(['icon:', 'bullet:'])->classes(
        'flex flex-col items-center flex-1 text-center gap-2',
        $fontSize(size: $size),
    )"
    :icon:class="$classes(
        'rounded-full text-white flex items-center justify-center font-semibold shrink-0',
        $widthHeight(size: $size, mode: 'large'),
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
                :attributes="$attributesAfter('icon:')"
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
            <span {{ $attributesAfter('bullet:')->classes(
                'bg-white rounded-full',
                $widthHeight(size: $size, mode: 'smallest')
            ) }}></span>
        @endif
    </x-slot:icon>

    {{ $slot }}
</tk:element>
