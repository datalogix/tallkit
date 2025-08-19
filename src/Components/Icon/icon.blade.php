<tk:tooltip.wrapper :$attributes>
    {!! Str::of($svg)->replace('<svg', '<svg '.$attributes->classes(match ($size) {
        'xs' => '[:where(&)]:size-4',
        'sm' => '[:where(&)]:size-5',
        default => '[:where(&)]:size-6',
        'lg' => '[:where(&)]:size-8',
        'xl' => '[:where(&)]:size-10',
        '2xl' => '[:where(&)]:size-12',
        '3xl' => '[:where(&)]:size-14',
    }, 'text-current')) !!}
</tk:tooltip.wrapper>
