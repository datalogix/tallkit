<tk:element
    x-data="badge"
    :name="$baseComponentKey()"
    :icon:size="$adjustSize()"
    :icon-trailing:size="$adjustSize()"
    content:class="flex items-center"
    :attributes="$attributes
        ->whereDoesntStartWith(['close:'])
        ->classes(
            '
            transition
            font-medium whitespace-nowrap
            [print-color-adjust:exact]
            ',
            match ($size) {
                'xs' => 'text-[11px] rounded-sm px-1 py-px gap-1',
                'sm' => 'text-xs rounded-sm px-1.5 py-0.5 gap-1',
                 default => 'text-sm rounded-md px-2 py-0.5 gap-1.5',
                'lg' => 'text-base rounded-md px-2.5 py-1 gap-1.5',
                'xl' => 'text-lg rounded-lg px-3 py-1 gap-2.5',
                '2xl' => 'text-xl rounded-lg px-3.5 py-1.5 gap-2.5',
                '3xl' => 'text-2xl rounded-xl px-4 py-1.5 gap-3',
            },
            match ($pill) {
                true => 'rounded-full '. match ($size) {
                    'xs' => 'px-2',
                    'sm' => 'px-3',
                    default => 'px-4',
                    'lg' => 'px-5',
                    'xl' => 'px-6',
                    '2xl' => 'px-7',
                    '3xl' => 'px-8',
                },
                default => '',
            },
            match ($border) {
                true => 'border',
                'solid' => 'border border-solid',
                'dashed' => 'border border-dashed',
                'dotted' => 'border border-dotted',
                default => '',
            }
        )
        ->when(
            $border,
            fn ($c) => $c->classes(match ($variant) {
                'accent' => 'border-[var(--color-accent)]',
                default => 'border-zinc-400',
                'red' => 'border-red-400',
                'orange' => 'border-orange-400',
                'amber' => 'border-amber-400',
                'yellow' => 'border-yellow-400',
                'lime' => 'border-lime-400',
                'green' => 'border-green-400',
                'emerald' => 'border-emerald-400',
                'teal' => 'border-teal-400',
                'cyan' => 'border-cyan-400',
                'sky' => 'border-sky-400',
                'blue' => 'border-blue-400',
                'indigo' => 'border-indigo-400',
                'violet' => 'border-violet-400',
                'purple' => 'border-purple-400',
                'fuchsia' => 'border-fuchsia-400',
                'pink' => 'border-pink-400',
                'rose' => 'border-rose-400',
            })
        )
        ->when(
            $solid,
            fn ($c) => $c->classes(match ($variant) {
                'accent' => 'text-[var(--color-accent-foreground)] bg-[var(--color-accent)] [&:is(button)]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_15%)]',
                default => 'text-white dark:text-white bg-zinc-600 dark:bg-zinc-600 [&:is(button)]:hover:bg-zinc-700 dark:[&:is(button)]:hover:bg-zinc-500',
                'red' => 'text-white dark:text-white bg-red-500 dark:bg-red-600 [&:is(button)]:hover:bg-red-600 dark:[&:is(button)]:hover:bg-red-500',
                'orange' => 'text-white dark:text-white bg-orange-500 dark:bg-orange-600 [&:is(button)]:hover:bg-orange-600 dark:[&:is(button)]:hover:bg-orange-500',
                'amber' => 'text-white dark:text-white bg-amber-500 dark:bg-amber-600 [&:is(button)]:hover:bg-amber-600 dark:[&:is(button)]:hover:bg-amber-500',
                'yellow' => 'text-white dark:text-white bg-yellow-500 dark:bg-yellow-600 [&:is(button)]:hover:bg-yellow-600 dark:[&:is(button)]:hover:bg-yellow-500',
                'lime' => 'text-white dark:text-white bg-lime-500 dark:bg-lime-600 [&:is(button)]:hover:bg-lime-600 dark:[&:is(button)]:hover:bg-lime-500',
                'green' => 'text-white dark:text-white bg-green-500 dark:bg-green-600 [&:is(button)]:hover:bg-green-600 dark:[&:is(button)]:hover:bg-green-500',
                'emerald' => 'text-white dark:text-white bg-emerald-500 dark:bg-emerald-600 [&:is(button)]:hover:bg-emerald-600 dark:[&:is(button)]:hover:bg-emerald-500',
                'teal' => 'text-white dark:text-white bg-teal-500 dark:bg-teal-600 [&:is(button)]:hover:bg-teal-600 dark:[&:is(button)]:hover:bg-teal-500',
                'cyan' => 'text-white dark:text-white bg-cyan-500 dark:bg-cyan-600 [&:is(button)]:hover:bg-cyan-600 dark:[&:is(button)]:hover:bg-cyan-500',
                'sky' => 'text-white dark:text-white bg-sky-500 dark:bg-sky-600 [&:is(button)]:hover:bg-sky-600 dark:[&:is(button)]:hover:bg-sky-500',
                'blue' => 'text-white dark:text-white bg-blue-500 dark:bg-blue-600 [&:is(button)]:hover:bg-blue-600 dark:[&:is(button)]:hover:bg-blue-500',
                'indigo' => 'text-white dark:text-white bg-indigo-500 dark:bg-indigo-600 [&:is(button)]:hover:bg-indigo-600 dark:[&:is(button)]:hover:bg-indigo-500',
                'violet' => 'text-white dark:text-white bg-violet-500 dark:bg-violet-600 [&:is(button)]:hover:bg-violet-600 dark:[&:is(button)]:hover:bg-violet-500',
                'purple' => 'text-white dark:text-white bg-purple-500 dark:bg-purple-600 [&:is(button)]:hover:bg-purple-600 dark:[&:is(button)]:hover:bg-purple-500',
                'fuchsia' => 'text-white dark:text-white bg-fuchsia-500 dark:bg-fuchsia-600 [&:is(button)]:hover:bg-fuchsia-600 dark:[&:is(button)]:hover:bg-fuchsia-500',
                'pink' => 'text-white dark:text-white bg-pink-500 dark:bg-pink-600 [&:is(button)]:hover:bg-pink-600 dark:[&:is(button)]:hover:bg-pink-500',
                'rose' => 'text-white dark:text-white bg-rose-500 dark:bg-rose-600 [&:is(button)]:hover:bg-rose-600 dark:[&:is(button)]:hover:bg-rose-500',
            }),
            fn ($c) => $c->classes(match ($variant) {
                'accent' => '
                    text-[var(--color-accent-foreground)]
                    bg-[color-mix(in_oklab,_var(--color-accent),_transparent_20%)]
                    dark:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_40%)]
                    [&:is(button)]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_30%)]
                    dark:[&:is(button)]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_50%)]
                ',
                default => 'text-zinc-700 dark:text-zinc-200 bg-zinc-400/15 dark:bg-zinc-400/40 [&:is(button)]:hover:bg-zinc-400/25 dark:[&:is(button)]:hover:bg-zinc-400/50',
                'red' => 'text-red-700 dark:text-red-200 bg-red-400/20 dark:bg-red-400/40 [&:is(button)]:hover:bg-red-400/30 dark:[&:is(button)]:hover:bg-red-400/50',
                'orange' => 'text-orange-700 dark:text-orange-200 bg-orange-400/20 dark:bg-orange-400/40 [&:is(button)]:hover:bg-orange-400/30 dark:[&:is(button)]:hover:bg-orange-400/50',
                'amber' => 'text-amber-700 dark:text-amber-200 bg-amber-400/25 dark:bg-amber-400/40 [&:is(button)]:hover:bg-amber-400/40 dark:[&:is(button)]:hover:bg-amber-400/50',
                'yellow' => 'text-yellow-800 dark:text-yellow-200 bg-yellow-400/25 dark:bg-yellow-400/40 [&:is(button)]:hover:bg-yellow-400/40 dark:[&:is(button)]:hover:bg-yellow-400/50',
                'lime' => 'text-lime-800 dark:text-lime-200 bg-lime-400/25 dark:bg-lime-400/40 [&:is(button)]:hover:bg-lime-400/35 dark:[&:is(button)]:hover:bg-lime-400/50',
                'green' => 'text-green-800 dark:text-green-200 bg-green-400/20 dark:bg-green-400/40 [&:is(button)]:hover:bg-green-400/30 dark:[&:is(button)]:hover:bg-green-400/50',
                'emerald' => 'text-emerald-800 dark:text-emerald-200 bg-emerald-400/20 dark:bg-emerald-400/40 [&:is(button)]:hover:bg-emerald-400/30 dark:[&:is(button)]:hover:bg-emerald-400/50',
                'teal' => 'text-teal-800 dark:text-teal-200 bg-teal-400/20 dark:bg-teal-400/40 [&:is(button)]:hover:bg-teal-400/30 dark:[&:is(button)]:hover:bg-teal-400/50',
                'cyan' => 'text-cyan-800 dark:text-cyan-200 bg-cyan-400/20 dark:bg-cyan-400/40 [&:is(button)]:hover:bg-cyan-400/30 dark:[&:is(button)]:hover:bg-cyan-400/50',
                'sky' => 'text-sky-800 dark:text-sky-200 bg-sky-400/20 dark:bg-sky-400/40 [&:is(button)]:hover:bg-sky-400/30 dark:[&:is(button)]:hover:bg-sky-400/50',
                'blue' => 'text-blue-800 dark:text-blue-200 dbg-blue-400/20 dark:bg-blue-400/40 [&:is(button)]:hover:bg-blue-400/30 dark:[&:is(button)]:hover:bg-blue-400/50',
                'indigo' => 'text-indigo-700 dark:text-indigo-200 bg-indigo-400/20 dark:bg-indigo-400/40 [&:is(button)]:hover:bg-indigo-400/30 dark:[&:is(button)]:hover:bg-indigo-400/50',
                'violet' => 'text-violet-700 dark:text-violet-200 bg-violet-400/20 dark:bg-violet-400/40 [&:is(button)]:hover:bg-violet-400/30 dark:[&:is(button)]:hover:bg-violet-400/50',
                'purple' => 'text-purple-700 dark:text-purple-200 bg-purple-400/20 dark:bg-purple-400/40 [&:is(button)]:hover:bg-purple-400/30 dark:[&:is(button)]:hover:bg-purple-400/50',
                'fuchsia' => 'text-fuchsia-700 dark:text-fuchsia-200 bg-fuchsia-400/20 dark:bg-fuchsia-400/40 [&:is(button)]:hover:bg-fuchsia-400/30 dark:[&:is(button)]:hover:bg-fuchsia-400/50',
                'pink' => 'text-pink-700 dark:text-pink-200 bg-pink-400/20 dark:bg-pink-400/40 [&:is(button)]:hover:bg-pink-400/30 dark:[&:is(button)]:hover:bg-pink-400/50',
                'rose' => 'text-rose-700 dark:text-rose-200 bg-rose-400/20 dark:bg-rose-400/40 [&:is(button)]:hover:bg-rose-400/30 dark:[&:is(button)]:hover:bg-rose-400/50',
            })
        )
    "
>
    {{ $slot }}

    @if ($close)
        <x-slot name="append">
            <tk:badge.close
                :attributes="$attributesAfter('close:')"
                :$size
            />
        </x-slot>
    @endif
</tk:element>
