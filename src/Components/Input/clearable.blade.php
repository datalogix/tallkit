<tk:button
    x-data="inputClearable"
    :attributes="$attributes->classes('
        mx-2 -me-1
        [[data-tallkit-input]:has(input:placeholder-shown)_&]:hidden
        [[data-tallkit-input]:has(input[disabled])_&]:hidden
        [[data-tallkit-input]:has(input:invalid)_&]:hidden
    ')"
    tabindex="-1"
    variant="none"
    tooltip="Clear input"
    icon="times"
    wire:replace.self
>
    {{ $slot }}
</tk:button>
