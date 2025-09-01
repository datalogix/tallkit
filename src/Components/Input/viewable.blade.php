<tk:button
    x-data="inputViewable"
    :attributes="$attributes->classes('mx-2 -me-1')"
    variant="none"
    aria-label="Toggle password visibility"
    tooltip="Toggle password visibility"
    icon="eye"
    icon:class="hidden"
    icon::class="{ 'hidden': !viewed }"
    icon-trailing="eye-off"
    icon-trailing:class="hidden"
    icon-trailing::class="{ 'hidden': viewed }"
>
    {{ $slot }}
</tk:button>
