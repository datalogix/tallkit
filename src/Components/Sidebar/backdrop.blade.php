
<div
    x-data
    x-on:click="$dispatch('sidebar-{{ $name }}-close')"
    {{
        $attributes->classes('
            z-10 fixed inset-0 bg-black/10 hidden
            [&:has(+[data-show-stashed-sidebar])]:block lg:[&:has(+[data-show-stashed-sidebar])]:hidden
        ')->merge([$dataKey() => $name])
    }}
></div>
