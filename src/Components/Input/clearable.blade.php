<tk:button
    x-data="inputClearable"
    :attributes="$attributes->classes('mx-2 -me-1 [[data-tallkit-input]:has(input:placeholder-shown)_&]:hidden [[data-tallkit-input]:has(input[disabled])_&]:hidden')"
    variant="none"
    aria-label="{{ __('Clear input') }}"
    icon="times"
    tabindex="-1"
>
    {{ $slot }}
</tk:button>
