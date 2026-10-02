@props([
    ...TALLKit::elementProps(),
    'target' => null,
    'content' => null,
])
<tk:button
    wire:ignore.self
    x-cloak
    x-data="copyable({{ Js::from($target) }}, {{ Js::from($content) }})"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->merge(['variant' => 'none'])->classes('min-w-6 min-h-6')->merge(['tooltip' => 'Copied'])"
    :aria-label="__('Copy')"
    ::aria-label="copied ? {{ Js::from(__('Copied')) }} : {{ Js::from(__('Copy')) }}"
    tooltip:open="manual"
    icon="clipboard-multiple"
    icon:class="hidden"
    icon::class="{ 'hidden': copied }"
    iconTrailing="clipboard-check-multiple"
    icon-trailing:class="hidden"
    icon-trailing::class="{ 'hidden': !copied }"
>
    {{ $slot }}
</tk:button>
