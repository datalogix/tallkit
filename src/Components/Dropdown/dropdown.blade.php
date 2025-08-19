<div
    x-data="dropdown({
        mode: @js($hover ? 'hover' : 'dropdown'),
        position: @js($position ?? 'bottom'),
        align: @js($align ?? 'start'),
    })"
    {{ $attributes->classes('inline-block') }}
>
    {{ $slot }}
</div>
