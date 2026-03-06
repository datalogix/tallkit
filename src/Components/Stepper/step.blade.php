<tk:element
    class="flex flex-col items-center flex-1 text-sm text-white text-center gap-2"
    :icon:class="$classes(
        'w-8 h-8 rounded-full text-white flex items-center justify-center font-semibold shrink-0',
        match ($status) {
            'completed' => 'bg-green-600',
            'active' => 'bg-blue-500',
            default => 'bg-gray-400',
        }
    )"
>
    <x-slot:icon>
        @if ($icon)
            <tk:icon
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
            <span class="h-2 w-2 bg-white rounded-full"></span>
        @endif
    </x-slot:icon>

    {{ $slot }}
</tk:element>
