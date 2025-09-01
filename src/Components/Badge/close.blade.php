<tk:button
    variant="none"
    aria-label="Close"
    tooltip="Close"
    :icon="$slot->isEmpty() ? $icon ?? 'close' : null"
    :attributes="$attributes->classes(
        'p-px -my-1 -me-1 text-current! opacity-50 hover:opacity-100',
        match ($size) {
            'xs' => '**:data-tallkit-icon:size-3',
            'sm' => '**:data-tallkit-icon:size-3.5',
            default => '**:data-tallkit-icon:size-4',
            'lg' => ' **:data-tallkit-icon:size-4.5',
            'xl' => '**:data-tallkit-icon:size-5',
            '2xl' => '**:data-tallkit-icon:size-5.5',
            '3xl' => '**:data-tallkit-icon:size-6',
        }
    )"
>
    {{ $slot }}
</tk:button>


