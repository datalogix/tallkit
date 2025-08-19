<tk:button
    x-data
    x-on:click="$dispatch('sidebar-{{ $name }}-toggle')"
    :attributes="$attributes->classes('shrink-0')->merge([$dataKey() => $name])"
    variant="subtle"
    aria-label="{{ __('Toggle sidebar') }}"
    icon="menu"
>
    {{ $slot }}
</tk:button>
