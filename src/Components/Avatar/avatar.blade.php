<tk:element :$tooltip :attributes="$attributes
    ->whereDoesntStartWith(['image:', 'initials:', 'icon:'])
    ->classes(
        'relative flex-none isolate flex items-center justify-center',
        '[:where(&)]:font-medium [:where(&)]:text-zinc-800 [:where(&)]:bg-zinc-200',
        '[:where(&)]:dark:bg-zinc-600 [:where(&)]:dark:text-white',
        'after:absolute after:inset-0 after:inset-ring-[1px] after:inset-ring-black/7 dark:after:inset-ring-white/10',
        match($size) {
            'xl' => '[:where(&)]:size-16 [:where(&)]:text-base **:data-tallkit-icon:size-10',
            'lg' => '[:where(&)]:size-12 [:where(&)]:text-base **:data-tallkit-icon:size-8',
            default => '[:where(&)]:size-10 [:where(&)]:text-sm **:data-tallkit-icon:size-6',
            'sm' => '[:where(&)]:size-8 [:where(&)]:text-sm **:data-tallkit-icon:size-5',
            'xs' => '[:where(&)]:size-6 [:where(&)]:text-xs **:data-tallkit-icon:size-4',
        },
        match($color) {
            'primary' => 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)]',
            'red' => 'bg-red-200 text-red-800',
            'orange' => 'bg-orange-200 text-orange-800',
            'amber' => 'bg-amber-200 text-amber-800',
            'yellow' => 'bg-yellow-200 text-yellow-800',
            'lime' => 'bg-lime-200 text-lime-800',
            'green' => 'bg-green-200 text-green-800',
            'emerald' => 'bg-emerald-200 text-emerald-800',
            'teal' => 'bg-teal-200 text-teal-800',
            'cyan' => 'bg-cyan-200 text-cyan-800',
            'sky' => 'bg-sky-200 text-sky-800',
            'blue' => 'bg-blue-200 text-blue-800',
            'indigo' => 'bg-indigo-200 text-indigo-800',
            'violet' => 'bg-violet-200 text-violet-800',
            'purple' => 'bg-purple-200 text-purple-800',
            'fuchsia' => 'bg-fuchsia-200 text-fuchsia-800',
            'pink' => 'bg-pink-200 text-pink-800',
            'rose' => 'bg-rose-200 text-rose-800',
            default => '',
        },
    )
    ->when($square,
        fn ($c) => $c->classes(match($size) {
            'xl' => 'after:rounded-2xl rounded-2xl',
            'lg' => 'after:rounded-xl rounded-xl',
            default => 'after:rounded-lg rounded-lg',
            'sm' => 'after:rounded-md rounded-md',
            'xs' => 'after:rounded-sm rounded-sm',
        }),
        fn ($c) => $c->classes('after:rounded-full rounded-full'),
    )
" data-tallkit-avatar>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $alt ?? $name }}" {{ $attributesAfter('image:')->classes($square ? 'rounded-sm' : 'rounded-full') }} data-tallkit-avatar-image>
    @elseif(($initials || $slot->isNotEmpty()) && !$icon)
        <span {{ $attributesAfter('initials:')->classes('select-none truncate m-px') }}
            data-tallkit-avatar-initials>{{ $initials ?? $slot }}</span>
    @else
        <tk:icon :attributes="$attributesAfter('icon:')->classes('opacity-75')" :icon="$icon ?? 'user'"
            data-tallkit-avatar-icon />
    @endif
</tk:element>
