@props([
    'variant' => null,
    'label' => null,
])
<div
    {{
        TALLKit::attributesAfter($attributes, 'container:')
            ->dataKey('menu-separator-container')
            ->classes('-mx-[.4rem] my-[.4rem] h-px')
    }}
>
    <tk:separator
        :attributes="$attributes->whereDoesntStartWith(['container:'])"
        :$variant
        :$label
     />
</div>
