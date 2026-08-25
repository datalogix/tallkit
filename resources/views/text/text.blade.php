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
            'strong' => '[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white',
            'subtle' => '[:where(&)]:text-zinc-400 dark:[:where(&)]:text-white/50',
            default => TALLKit::text($variant) ?? '[:where(&)]:text-zinc-700 dark:[:where(&)]:text-white/80',
        }
    )"
>
    {{ $slot }}
</tk:element.wrapper>
