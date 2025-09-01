<tk:button
    variant="none"
    tooltip="Close"
    aria-label="Close"
    :icon="$slot->isEmpty() ? $icon ?? 'close' : null"
    :attributes="$attributes->classes(
        'rounded-md p-2 focus:ring-2',
        match ($type) {
            'danger' => 'text-red-800 dark:text-red-300 focus:ring-red-300 hover:text-red-800 hover:bg-red-200 dark:hover:text-red-800 dark:hover:bg-red-200',
            'success' => 'text-green-800 dark:text-green-300 focus:ring-green-300 hover:text-green-800 hover:bg-green-200 dark:hover:text-green-800 dark:hover:bg-green-200',
            'warning' => 'text-yellow-800 dark:text-yellow-300 focus:ring-yellow-300 hover:text-yellow-800 hover:bg-yellow-200 dark:hover:text-yellow-800 dark:hover:bg-yellow-200',
            'info' => 'text-blue-800 dark:text-blue-300 focus:ring-blue-300 hover:text-blue-800 hover:bg-blue-200 dark:hover:text-blue-800 dark:hover:bg-blue-200',
            default => 'text-zinc-800 dark:text-zinc-300 focus:ring-zinc-300 hover:text-zinc-800 hover:bg-zinc-200 dark:hover:text-zinc-800 dark:hover:bg-zinc-200',
        },
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

