<tk:menu.item
    :attributes="$attributes->whereDoesntStartWith(['selector:'])->classes('data-active:bg-transparent!')"
    as="div"
    label="Theme"
    icon="palette-outline"
    keep-open
>
    <x-slot:append>
        <tk:appearance.selector
            :attributes="TALLKit::attributesAfter($attributes, 'selector:')->classes('ms-4')"
            size="xs"
        />
    </x-slot:append>
</tk:menu.item>
