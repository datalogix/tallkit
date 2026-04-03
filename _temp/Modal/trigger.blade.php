<div
    x-data="modalTrigger(@js($name), @js($shortcut))"
    {{ $attributes->classes('contents') }}
>
    {{ $slot }}
</div>
