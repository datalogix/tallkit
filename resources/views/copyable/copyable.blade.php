@props([
    'target' => null,
    'content' => null,
    'variant' => 'none',
])
<tk:button
    wire:replace.self
    x-cloak
    x-data="copyable({{ Js::from($target) }}, {{ Js::from($content) }})"
    :$attributes
    :$variant
    label="Copy to clipboard"
    tooltip="Copied"
    tooltip:mode="manual"
    icon="clipboard-multiple"
    icon:class="hidden"
    icon::class="{ 'hidden': copied }"
    iconTrailing="clipboard-check-multiple"
    icon-trailing:class="hidden"
    icon-trailing::class="{ 'hidden': !copied }"
>
    {{ $slot }}
</tk:button>
