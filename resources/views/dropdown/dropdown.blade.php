@props([
    'open' => null,
    'position' => null,
    'align' => null,
    'animation' => null,
])
<div
    wire:ignore.self
    x-data="popover({
        mode: @js(match ($open) { null, 'click' => 'dropdown', default => $open }),
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
