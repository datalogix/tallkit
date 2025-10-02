<tk:button
    x-data="inputCopyable"
    :attributes="$attributes->classes('mx-2 -me-1')"
    tabindex="-1"
    variant="none"
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
