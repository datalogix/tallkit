@props([
    'name' => null,
    'shortcut' => null,
])
<div
    x-data="modalTrigger({{ Js::from(['name' => $name, 'shortcut' => $shortcut]) }})"
    {{ $attributes->classes('contents') }}
>
    {{ $slot }}
</div>
