@props([
    'name' => null,
])
<div
    x-data
    x-on:click="$dispatch({{ Js::from(TALLKit::eventName('sidebar-close')) }}, { name: {{ Js::from($name) }} })"
    {{
        $attributes->classes('
            z-15 fixed inset-0 bg-black/50 hidden
            [&:has(+[data-show-stashed-sidebar])]:block
            lg:[&:has(+[data-show-stashed-sidebar])]:hidden
        ')->dataKey('sidebar-backdrop', $name)
    }}
></div>
