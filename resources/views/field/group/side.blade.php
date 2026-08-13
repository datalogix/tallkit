@props([
    'side' => null,
    'size' => null,
    ...TALLKit::elementProps(),
])
<tk:element
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
    :attributes="$attributes->dataKey('input-group-'.$side)->classes(
        '
            px-4 whitespace-nowrap
            [:where(&)]:text-zinc-800 dark:[:where(&)]:text-white/85
            [:where(&)]:bg-zinc-800/5 dark:[:where(&)]:bg-white/20
            border-zinc-200 dark:border-white/10
            border-t border-b shadow-xs
        ',
        $side === 'suffix' ? 'rounded-e-lg border-e' : 'rounded-s-lg border-s',
        TALLKit::fontSize(size: $size),
    )"
>
    {{ $slot }}
</tk:element>
