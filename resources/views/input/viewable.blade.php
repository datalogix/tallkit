@props([
    ...TALLKit::elementProps(),
])
<tk:button
    wire:ignore.self
    x-cloak
    x-data="inputViewable()"
    ::aria-pressed="viewed ? 'true' : 'false'"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())
        ->merge(['variant' => 'none', 'tooltip' => 'Toggle password visibility'])
        ->classes('min-w-6 min-h-6')"
    icon="eye"
    icon:class="hidden"
    icon::class="{ 'hidden': !viewed }"
    iconTrailing="eye-off"
    icon-trailing:class="hidden"
    icon-trailing::class="{ 'hidden': viewed }"
>
    {{ $slot }}
</tk:button>
