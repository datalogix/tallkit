@props([
    'open' => null,
    'position' => null,
    'align' => null,
    'animation' => null,
])
<div
    wire:ignore.self
    x-data="popover({{ Js::from([
        'mode' => match ($open) { null, 'click' => 'dropdown', default => $open },
        'position' => $position ?? 'bottom',
        'align' => $align ?? 'start',
    ]) }})"
    {{
        $attributes
            ->dataKey('dropdown')
            ->dataKey($open === 'context' ? 'context' : null)
            ->classes([
                'inline-flex',
                'pointer-coarse:select-none pointer-coarse:[-webkit-touch-callout:none]' => $open === 'context',
            ])
    }}
>
    {{ $slot }}
</div>
