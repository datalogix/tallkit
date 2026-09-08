@aware(['size'])
@props([
    'size' => null,
    'items' => null,
    'animate' => null,
])
<tk:dropdown :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'dropdown:')">
    <tk:nav.item
        :attributes="$attributes->except(['href'])
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
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'menu:')"
        :$items
        :$size
    >
        {{ $slot }}
    </tk:menu>
</tk:dropdown>
