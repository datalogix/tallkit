<div
    x-data="submenu"
    {{ $attributesAfter('container:') }}
>
    <tk:menu.item
        :attributes="$attributes->whereDoesntStartWith(['container:', 'menu:'])"
        keep-open
        icon-trailing="chevron-right"
        icon-trailing:class="rtl:rotate-180"
    />

    <tk:menu :attributes="$attributesAfter('menu:')->classes('-ml-2')">
        {{ $slot }}
    </tk:menu>
</div>
