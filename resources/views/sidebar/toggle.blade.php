@props([
    'name' => null,
])
<tk:button
    x-data="{ expanded: false }"
    x-on:click="$dispatch('sidebar-{{ $name }}-toggle')"
    x-on:sidebar-{{ $name }}-state.window="expanded = $event.detail.opened"
    :attributes="$attributes->classes('shrink-0')->merge([
        TALLKit::dataKey('sidebar-toggle') => $name,
        ':aria-expanded' => 'expanded',
        'aria-controls' => TALLKit::generateId('sidebar', $name),
    ])"
    variant="subtle"
    tooltip="Toggle sidebar"
    icon="menu"
>
    {{ $slot }}
</tk:button>
