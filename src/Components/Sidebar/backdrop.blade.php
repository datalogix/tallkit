
<div
    x-data
    x-on:click="$dispatch('sidebar-{{ $name }}-close')"
    {{
        $attributes->classes('
            z-15 fixed inset-0 bg-black/50 hidden
            [&:has(+[data-show-stashed-sidebar])]:block lg:[&:has(+[data-show-stashed-sidebar])]:hidden
        ')->merge([$dataKey() => $name])
    }}
></div>
