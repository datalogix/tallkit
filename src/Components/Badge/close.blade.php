<tk:button
    variant="none"
    tooltip="Close"
    :icon="$slot->isEmpty() ? $icon ?? 'close' : null"
    :attributes="$attributes->classes(
        'p-px -my-1 -me-1 text-current! opacity-50 hover:opacity-100',
        match ($size) {
            'xs' => '[&_[data-tallkit-icon]]:size-3',
            'sm' => '[&_[data-tallkit-icon]]:size-3.5',
            default => '[&_[data-tallkit-icon]]:size-4',
            'lg' => '[&_[data-tallkit-icon]]:size-4.5',
            'xl' => '[&_[data-tallkit-icon]]:size-5',
            '2xl' => '[&_[data-tallkit-icon]]:size-5.5',
            '3xl' => '[&_[data-tallkit-icon]]:size-6',
        }
    )"
>
    {{ $slot }}
</tk:button>


