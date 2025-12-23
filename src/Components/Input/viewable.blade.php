<tk:button
    x-data="inputViewable"
    :attributes="$attributes->classes('mx-2 -me-1')"
    :variant="$attributes->pluck('variant', 'none')"
    tabindex="-1"
    tooltip="Toggle password visibility"
    icon="eye"
    icon:class="hidden"
    icon::class="{ 'hidden': !viewed }"
    icon-trailing="eye-off"
    icon-trailing:class="hidden"
    icon-trailing::class="{ 'hidden': viewed }"
    wire:replace.self
>
    {{ $slot }}
</tk:button>
