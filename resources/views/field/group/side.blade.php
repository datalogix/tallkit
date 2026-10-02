@props([
    ...TALLKit::elementProps(),
    'side' => null,
    'size' => null,
])
<tk:element
    content:class="min-w-0 truncate"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->dataKey('input-group-'.$side)->classes(
        '
            px-4 whitespace-nowrap max-w-1/2 min-w-0
            [:where(&)]:text-zinc-800 dark:[:where(&)]:text-white/85
            border-zinc-200 dark:border-white/10
            border-t border-b shadow-xs
        ',
        TALLKit::backgroundNeutral(variant: 'faint', prefix: '[:where(&)]:'),
        $side === 'suffix' ? 'rounded-e-lg border-e' : 'rounded-s-lg border-s',
        TALLKit::fontSize(size: $size),
    )"
>
    {{ $slot }}
</tk:element>
