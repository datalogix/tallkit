@aware(['size'])
@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'items' => null,
    'animate' => null,
    'keepOpen' => null,
    'animation' => null,
])
<tk:dropdown :attributes="$attributes->prefixed('dropdown:')">
    <tk:nav.item
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->except(['href'])
            ->whereDoesntStartWith(['dropdown:', 'menu:'])
            ->when($animate !== false, fn ($attrs) => $attrs->merge([
                'icon-trailing:class' => 'transition-transform',
                'icon-trailing::class' => '{ \'rotate-180\': opened }',
            ]))
        "
        :$size
        type="button"
        iconTrailing="chevron-down"
    >
        {{ $label ?? '' }}
    </tk:nav.item>

    <tk:menu
        :attributes="$attributes->prefixed('menu:')"
        :$items
        :$size
        :$keepOpen
        :$animation
    >
        {{ $slot }}
    </tk:menu>
</tk:dropdown>
