<div
    x-data="popover({
        mode: @js($mode ?? ($hover ? 'hover' : 'dropdown')),
        position: @js($position ?? 'bottom'),
        align: @js($align ?? 'start'),
    })"
    {{ $attributes->classes('inline-flex') }}
>
    {{ $slot }}
</div>
