<div {{ $attributes->classes(
    'flex isolate -space-x-2 rtl:space-x-reverse',
    '*:not-first:-ml-2 **:data-tallkit-avatar:ring-white **:data-tallkit-avatar:dark:ring-zinc-900',
    match ($size) {
        'xl' => '**:data-tallkit-avatar:ring-4 **:data-tallkit-avatar:size-16 **:data-tallkit-avatar:text-base **:data-tallkit-avatar:**:data-tallkit-icon:size-10',
        'lg' => '**:data-tallkit-avatar:ring-4 **:data-tallkit-avatar:size-12 **:data-tallkit-avatar:text-base **:data-tallkit-avatar:**:data-tallkit-icon:size-8',
        default => '**:data-tallkit-avatar:ring-4 **:data-tallkit-avatar:size-10 **:data-tallkit-avatar:text-sm **:data-tallkit-avatar:**:data-tallkit-icon:size-6',
        'sm' => '**:data-tallkit-avatar:ring-2 **:data-tallkit-avatar:size-8 **:data-tallkit-avatar:text-sm **:data-tallkit-avatar:**:data-tallkit-icon:size-5',
        'xs' => '**:data-tallkit-avatar:ring-2 **:data-tallkit-avatar:size-6 **:data-tallkit-avatar:text-xs **:data-tallkit-avatar:**:data-tallkit-icon:size-4',
    }
)->when(
        $square,
        fn($c) => $c->classes(match ($size) {
            'xl' => '**:data-tallkit-avatar:after:rounded-2xl **:data-tallkit-avatar:rounded-2xl',
            'lg' => '**:data-tallkit-avatar:after:rounded-xl **:data-tallkit-avatar:rounded-xl',
            default => '**:data-tallkit-avatar:after:rounded-lg **:data-tallkit-avatar:rounded-lg',
            'sm' => '**:data-tallkit-avatar:after:rounded-md **:data-tallkit-avatar:rounded-md',
            'xs' => '**:data-tallkit-avatar:after:rounded-sm **:data-tallkit-avatar:rounded-sm',
        }),
        fn($c) => $c->classes('**:data-tallkit-avatar:after:rounded-full **:data-tallkit-avatar:rounded-full'),
    ) }}
    data-tallkit-avatar-group>
    {{ $slot }}
</div>
