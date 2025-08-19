<tk:element
    :name="$baseComponentKey()"
    :$tooltip
    :attributes="$attributes
        ->whereDoesntStartWith(['image:', 'initials:', 'icon:'])
        ->classes(
            'inline-flex justify-center',
            'relative flex-none isolate [:where(&)]:font-medium',
            'after:absolute after:inset-0 after:inset-ring-[1px] after:inset-ring-black/7 dark:after:inset-ring-white/10',
            '[:where(&)]:bg-zinc-200 [:where(&)]:dark:bg-zinc-600 [:where(&)]:text-zinc-800 [:where(&)]:dark:text-white',
            match ($size) {
                'xs' => '[:where(&)]:size-6 [:where(&)]:text-[11px]',
                'sm' => '[:where(&)]:size-8 [:where(&)]:text-xs',
                default => '[:where(&)]:size-10 [:where(&)]:text-sm',
                'lg' => '[:where(&)]:size-12 [:where(&)]:text-base',
                'xl' => '[:where(&)]:size-16 [:where(&)]:text-lg',
                '2xl' => '[:where(&)]:size-20 [:where(&)]:text-xl',
                '3xl' => '[:where(&)]:size-24 [:where(&)]:text-2xl',
            },
            match ($variant === 'auto' ? $generateColor() : $variant) {
                'accent' => 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)]',
                'filled' => 'bg-zinc-800/5 dark:bg-white/10',
                'outline' => '',
                'ghost' => 'bg-transparent',
                'subtle' => 'bg-transparent text-zinc-500',
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
        ->when(
            $square,
            fn ($c) => $c->classes(match ($size) {
                'xs' => 'after:rounded-sm rounded-sm',
                'sm' => 'after:rounded-sm rounded-sm',
                default => 'after:rounded-md rounded-md',
                'lg' => 'after:rounded-md rounded-md',
                'xl' => 'after:rounded-lg rounded-lg',
                '2xl' => 'after:rounded-lg rounded-lg',
                '3xl' => 'after:rounded-xl rounded-xl',
            }),
            fn ($c) => $c->classes('after:rounded-full rounded-full'),
        )
    "
>
    @if ($src)
        <img
            {{
                $attributesAfter('image:')
                    ->classes($square ? 'rounded-sm' : 'rounded-full')
                    ->merge(['src' => $src, 'alt' => $alt ?? $name])
            }}
        />
    @elseif (($initials || $slot->isNotEmpty()) && !$icon)
        <span {{ $attributesAfter('initials:')->classes('select-none truncate m-px') }}>
            {{ $initials ?? $slot }}
        </span>
    @else
        <tk:icon
            :attributes="$attributesAfter('icon:')->classes('shrink-0 opacity-75')"
            :icon="is_string($icon) ? $icon : 'user'"
            :$size
        />
    @endif
</tk:element>
