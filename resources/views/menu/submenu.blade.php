@aware(['size', 'animation'])
@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'keepOpen' => null,
    'animation' => null,
])
<div
    wire:ignore.self
    x-data="submenu"
    {{
        $attributes->prefixed('container:')
    }}
>
    <tk:menu.item
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->whereDoesntStartWith(['container:', 'menu:'])"
        :$size
        keepOpen
        iconTrailing="chevron-right"
        icon-trailing:class="rtl:rotate-180"
    />

    <tk:menu
        :attributes="$attributes->prefixed('menu:')"
        :$size
        :$keepOpen
        :$animation
    >
        {{ $slot }}
    </tk:menu>
</div>
