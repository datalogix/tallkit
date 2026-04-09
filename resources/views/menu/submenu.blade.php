<div
    x-data="submenu"
    {{ TALLKit::attributesAfter($attributes, 'container:') }}
>
    <tk:menu.item
        :attributes="$attributes->whereDoesntStartWith(['container:', 'menu:'])"
        keepOpen
        iconTrailing="chevron-right"
        icon-trailing:class="rtl:rotate-180"
    />

    <tk:menu :attributes="TALLKit::attributesAfter($attributes, 'menu:')->classes('-ml-2')">
        {{ $slot }}
    </tk:menu>
</div>
