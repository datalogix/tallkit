<div
    x-data="modalTrigger(@js($name), @js($shortcut))"
    {{ $attributes->class('contents') }}
>
    {{ $slot }}
</div>
