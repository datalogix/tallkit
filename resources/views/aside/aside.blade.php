@props([
    'sticky' => null,
])
<div {{
    $attributes
        ->dataKey('aside')
        ->classes([
            '[grid-area:aside]',
            'overflow-y-auto' => $sticky
        ])
        ->mergeDefined(['x-data' => $sticky ? 'aside' : null, 'wire:ignore.self' => $sticky ? true : null])
}}>
    {{ $slot }}
</div>
