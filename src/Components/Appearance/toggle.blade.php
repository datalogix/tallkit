<tk:button
    x-data
    x-on:click="$tallkit.appearance.toggle({{ $animate === false ? null : 'event' }}, {{ is_array($animate) ? Js::from($animate) : 'null' }})"
    :$attributes
    variant="subtle"
    aria-label="Toggle light and dark mode"
    tooltip="Toggle light and dark mode"
    icon="ph:sun"
    icon:class="block dark:hidden"
    icon-trailing="ph:moon"
    icon-trailing:class="hidden dark:block"
>
    {{ $slot }}
</tk:button>
