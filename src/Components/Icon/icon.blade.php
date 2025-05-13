{!! $svg->replace('<svg', '<svg '.$attributes->classes(match($size) {
    'xl' => 'size-14',
    'lg' => 'size-10',
    'md' => 'size-8',
    'sm' => 'size-6',
    'xs' => 'size-4',
    default => 'size-6'
})) !!}
