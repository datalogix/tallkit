@props([
    'name' => null,
    'shortcut' => null,
])
<div
    x-data="modalTrigger({ name: @js($name), shortcut: @js($shortcut) })"
    {{ $attributes->classes('contents') }}
>
    {{ $slot }}
</div>
