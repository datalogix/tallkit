@props([
    'size' => null,
    'mode' => null,
    'weight' => null,
    'variant' => null,

    // element
    'label' => null,
    'icon' => null,
    'prefix' => null,
    'suffix' => null,
    'iconTrailing' => null,
    'info' => null,
    'badge' => null,
    'prepend' => null,
    'append' => null,
    'kbd' => null,
])
<tk:element.wrapper
    name="text"
    as="p"
    :$label
    :$icon
    :$prefix
    :$suffix
    :$iconTrailing
    :$info
    :$badge
    :$prepend
    :$append
    :$kbd
    :attributes="$attributes->classes(
        TALLKit::fontSize(size: $size, mode: $mode, weight: $weight),
        TALLKit::iconSize(size: $size, mode: $mode),
        match ($variant) {
            'accent' => 'text-[var(--color-accent-content)]',
            'strong' => TALLKit::textNeutral(variant: 'strong', prefix: '[:where(&)]:'),
            'subtle' => TALLKit::textNeutral(variant: 'subtle', prefix: '[:where(&)]:'),
            default => TALLKit::text(color: $variant) ?? TALLKit::textNeutral(prefix: '[:where(&)]:'),
        }
    )"
>
    {{ $slot }}
</tk:element.wrapper>
