@props([
    'label' => null,
    'size' => null,
    'variant' => null,
])
@if ($slot->hasActualContent() || $label)
    <tk:element
        name="kbd"
        as="kbd"
        :attributes="$attributes->classes(
            TALLKit::fontSize(size: $size, weight: true),
            '
                pointer-events-none rounded
                text-zinc-500 dark:text-zinc-400
            ',
            match ($variant) {
                'text' => 'bg-transparent',
                default => TALLKit::classes(
                    TALLKit::paddingInline(size: TALLKit::adjustSize(size: $size), mode: 'small'),
                    TALLKit::paddingBlock(size: TALLKit::adjustSize(size: $size), mode: 'smallest'),
                    'bg-zinc-800/10 dark:bg-white/10',
                ),
            },
        )"
        :$label
    >
        {{ $slot }}
    </tk:element>
@endif
