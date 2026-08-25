@props([
    'mode' => null,
    'hover' => null,
    'position' => null,
    'align' => null,
    'animation' => null,
])
<div
    wire:ignore.self
    x-data="popover({
        mode: @js($mode ?? ($hover ? 'hover' : 'dropdown')),
        position: @js($position ?? 'bottom'),
        align: @js($align ?? 'start'),
    })"
    {{
        $attributes
            ->dataKey('dropdown')
            ->classes('inline-flex')
    }}
>
    {{ $slot }}
</div>
