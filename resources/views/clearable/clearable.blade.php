<tk:button
    wire:replace.self
    x-cloak
    x-data="clearable()"
    :attributes="$attributes->classes('
        [[data-tallkit-control]:has(:placeholder-shown)_&]:hidden
        [[data-tallkit-control]:has(:disabled)_&]:hidden
    ')"
    :variant="$attributes->pluck('variant', 'none')"
    tooltip="Clear input"
    icon="close"
>
    {{ $slot }}
</tk:button>
