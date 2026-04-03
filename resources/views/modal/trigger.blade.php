@props([
    'name' => null,
    'shortcut' => null,
])
<div
    x-data="modalTrigger(@js($name), @js($shortcut))"
    {{ $attributes->classes('contents') }}
>
    {{ $slot }}
</div>
