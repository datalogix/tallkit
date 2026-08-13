@aware(['size'])
@props(['size' => null])
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
    >
        {{ $slot }}
    </tk:menu>
</div>
