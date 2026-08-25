@aware(['size', 'animation'])
@props(['size' => null, 'animation' => null])
<div
    wire:ignore.self
    x-data="submenu"
    {{ TALLKit::attributesAfter($attributes, 'container:') }}
>
    <tk:menu.item
        :attributes="$attributes->whereDoesntStartWith(['container:', 'menu:'])"
        :$size
        keepOpen
        iconTrailing="chevron-right"
        icon-trailing:class="rtl:rotate-180"
    />

    <tk:menu
        :attributes="TALLKit::attributesAfter($attributes, 'menu:')->classes('-ml-2')"
        :$size
        :$animation
    >
        {{ $slot }}
    </tk:menu>
</div>
