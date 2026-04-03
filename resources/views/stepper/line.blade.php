@props([
    'size' => null,
])
<div {{ $attributes->classes(
    'h-px flex-1 bg-gray-300 dark:bg-gray-500',
    match($size) {
        'xs' => 'mt-3',
        'sm' => 'mt-3.5',
        default => 'mt-4',
        'lg' => 'mt-4.5',
        'xl' => 'mt-5',
        '2xl' => 'mt-6',
        '3xl' => 'mt-8',
    },
) }}></div>
