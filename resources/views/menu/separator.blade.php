@props([
    'label' => null,
])
<div
    {{
        $attributes->prefixed('container:')
            ->dataKey('menu-separator-container')
            ->classes('-mx-[.4rem] my-[.4rem]', ['h-px' => ! $label && ! $slot->hasActualContent()])
    }}
>
    <tk:separator
        :attributes="$attributes->whereDoesntStartWith(['container:'])"
        :$label
    >
        {{ $slot }}
    </tk:separator>
</div>
