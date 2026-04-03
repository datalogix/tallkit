<div {{
    $attributes
        ->classes([
            '[grid-area:aside]',
            'max-h-[100dvh] overflow-y-auto' => $sticky
        ])
        ->merge($sticky ? ['x-data' => 'aside'] : [])
}}>
    {{ $slot }}
</div>
