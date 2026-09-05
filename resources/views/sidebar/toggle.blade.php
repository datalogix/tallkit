@props([
    'name' => null,
])
<tk:button
    x-data="{ expanded: false }"
    x-on:click="$dispatch('sidebar-{{ $name }}-toggle')"
    :attributes="$attributes->classes('shrink-0')->merge([
        TALLKit::dataKey(name: 'sidebar-toggle') => $name,
        'x-on:sidebar-'.$name.'-state.window' => 'expanded = $event.detail.opened',
        ':aria-expanded' => 'expanded',
        'aria-controls' => TALLKit::generateId(prefix: 'sidebar', name: $name),
    ])"
    variant="subtle"
    tooltip="Toggle sidebar"
    icon="menu"
>
    {{ $slot }}
</tk:button>
