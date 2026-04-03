<tk:button
    wire:replace.self
    x-data="inputCopyable"
    :$attributes
    :variant="$attributes->pluck('variant', 'none')"
    tabindex="-1"
    aria-label="Copy to clipboard"
    tooltip="Copied"
    tooltip:mode="manual"
    icon="clipboard-multiple"
    icon:class="hidden"
    icon::class="{ 'hidden': copied }"
    icon-trailing="clipboard-check-multiple"
    icon-trailing:class="hidden"
    icon-trailing::class="{ 'hidden': !copied }"
>
    {{ $slot }}
</tk:button>
