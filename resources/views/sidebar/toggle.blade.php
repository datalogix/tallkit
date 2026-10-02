@props([
    ...TALLKit::elementProps(),
    'name' => null,
])
<tk:button
    x-data="{ expanded: false }"
    x-on:click="$dispatch({{ Js::from(TALLKit::eventName('sidebar-toggle')) }}, { name: {{ Js::from($name) }} })"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->classes('shrink-0')->dataKey('sidebar-toggle', $name)->merge([
        'variant' => 'subtle',
        'tooltip' => 'Toggle sidebar',
        'x-on:'.TALLKit::eventName('sidebar-state').'.window' => '($event.detail.name ?? null) === '.Js::from($name).' && (expanded = $event.detail.opened)',
        ':aria-expanded' => 'expanded',
        'aria-controls' => TALLKit::generateId(prefix: 'sidebar', name: $name ?? 'main'),
    ])"
    icon="menu"
>
    {{ $slot }}
</tk:button>
