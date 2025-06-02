{!! str($svg)->replace('<svg', '<svg data-tallkit-icon '.$attributes->classes(match($size) {
    'xs' => '[:where(&)]:size-3',
    'sm' => '[:where(&)]:size-4',
    default => '[:where(&)]:size-6',
    'lg' => '[:where(&)]:size-10',
    'xl' => '[:where(&)]:size-12',
    '2xl' => '[:where(&)]:size-14',
    '3xl' => '[:where(&)]:size-16',
}, 'text-current')) !!}
