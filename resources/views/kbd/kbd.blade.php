@props([
    'label' => null,
    'size' => null,
    'variant' => null,
])
@if ($slot->hasActualContent() || $label)
    <tk:element
        name="kbd"
        :attributes="$attributes->classes(
            TALLKit::fontSize(size: $size, weight: true),
            'pointer-events-none ms-auto rounded',
            match ($variant) {
                'text' => 'text-zinc-400',
                default => '
                    px-1 py-0.5
                    bg-zinc-800/10 dark:bg-white/10
                    text-zinc-500 dark:text-zinc-300
                ',
            },
        )"
        :$label
    >
        {{ $slot }}
    </tk:element>
@endif
