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
        ->merge($sticky ? ['x-data' => 'aside'] : [])
}}>
    {{ $slot }}
</div>
