@props([
    ...TALLKit::elementProps(),
    'label' => null,
    'size' => null,
    'variant' => null,
])
@if ($slot->hasActualContent() || $label)
    <tk:element
        kind="kbd"
        as="kbd"
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->classes(
            TALLKit::fontSize(size: $size, weight: true),
            'pointer-events-none rounded',
            'text-zinc-600 dark:text-zinc-300',
            match ($variant) {
                'text' => 'bg-transparent',
                default => TALLKit::classes(
                    TALLKit::paddingInline(size: TALLKit::adjustSize(size: $size), mode: 'small'),
                    TALLKit::paddingBlock(size: TALLKit::adjustSize(size: $size), mode: 'smallest'),
                    TALLKit::backgroundNeutral(),
                ),
            },
        )"
        :$label
    >
        {{ $slot }}
    </tk:element>
@endif
