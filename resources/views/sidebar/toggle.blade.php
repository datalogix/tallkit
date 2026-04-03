@props([
    'name' => null,
])
<tk:button
    x-data
    x-on:click="$dispatch('sidebar-{{ $name }}-toggle')"
    :attributes="$attributes->classes('shrink-0')->merge([TALLKit::dataKey('sidebar-toggle') => $name])"
    variant="subtle"
    tooltip="Toggle sidebar"
    icon="menu"
>
    {{ $slot }}
</tk:button>
