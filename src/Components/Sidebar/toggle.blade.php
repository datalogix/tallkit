<tk:button
    x-data
    x-on:click="$dispatch('sidebar-{{ $name }}-toggle')"
    :attributes="$attributes->classes('shrink-0')->merge([$dataKey() => $name])"
    variant="subtle"
    aria-label="Toggle sidebar"
    tooltip="Toggle sidebar"
    icon="bars-2"
>
    {{ $slot }}
</tk:button>
