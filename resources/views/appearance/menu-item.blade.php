@props([
    ...TALLKit::elementProps(),
    'label' => 'Theme',
    'icon' => 'palette-outline',
])
<tk:menu.item
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->whereDoesntStartWith(['selector:'])->classes('data-active:bg-transparent!')"
    as="div"
    keepOpen
>
    <x-slot:append>
        <tk:appearance.selector
            :attributes="$attributes->prefixed('selector:')->classes('ms-4')"
            size="xs"
        />
    </x-slot:append>
</tk:menu.item>
