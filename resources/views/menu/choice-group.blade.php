@aware(['size'])
@props([
    'type' => 'checkbox',
    'size' => null,
    'keepOpen' => null,
    'items' => null,
    'value' => null,
    'label' => null,
])
<tk:menu.group
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['label' => null])->whereDoesntStartWith(['item:'])->merge(['data-keep-open' => $keepOpen])"
    :$size
    wire:ignore.self
    x-data="{ value: {{ Js::from($type === 'radio' ? $value : ($value ?? [])) }}, menuGroup: true }"
    x-modelable="value"
>
    @foreach (collect($items) as $key => $item)
        <tk:menu.choice
            :$type
            :attributes="$attributes->prefixed('item:')
                ->merge(TALLKit::attributesFromItem(is_array($item)
                    ? $item + ['value' => is_string($key) ? $key : ($item['label'] ?? null)]
                    : ['label' => $item, 'value' => array_is_list(collect($items)->all()) ? $item : $key]), false)
            "
            :$size
        />
    @endforeach

    {{ $slot }}
</tk:menu.group>
