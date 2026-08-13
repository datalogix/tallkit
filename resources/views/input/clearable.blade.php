<tk:button
    wire:replace.self
    x-cloak
    x-data="inputClearable()"
    :attributes="$attributes->classes('
        [[data-tallkit-control]:has(:placeholder-shown)_&]:hidden
        [[data-tallkit-control]:has(:disabled)_&]:hidden
        [[data-tallkit-control]:has(:invalid)_&]:hidden
    ')"
    :variant="$attributes->pluck('variant', 'none')"
    tabindex="-1"
    tooltip="Clear input"
    icon="close"
>
    {{ $slot }}
</tk:button>
