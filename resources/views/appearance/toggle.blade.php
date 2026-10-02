@props([
    ...TALLKit::elementProps(),
    'animate' => null,
])
<tk:button
    x-data
    x-on:click="$tallkit.appearance.toggle({{ $animate === false ? 'null' : 'event' }}, {{ is_array($animate) ? Js::from($animate) : 'null' }})"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())
        ->merge(['variant' => 'subtle', 'tooltip' => 'Toggle light and dark mode'])"
    icon="ph:sun"
    icon:class="block dark:hidden"
    iconTrailing="ph:moon"
    icon-trailing:class="hidden dark:block"
>
    {{ $slot }}
</tk:button>
