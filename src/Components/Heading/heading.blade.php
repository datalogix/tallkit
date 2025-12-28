<tk:element.wrapper
    :name="$baseComponentKey()"
    :attributes="$attributes->classes(
        match ($size) {
            'xs' => 'text-sm font-medium',
            'sm' => 'text-base font-medium',
            default => 'text-lg font-semibold',
            'lg' => 'text-xl font-semibold',
            'xl' => 'text-2xl font-bold',
            '2xl' => 'text-3xl font-bold',
            '3xl' => 'text-4xl font-extrabold',
        },
        match ($variant) {
            'accent' => 'text-[var(--color-accent-content)]',
            'strong' => '[:where(&)]:text-zinc-900 dark:[:where(&)]:text-white',
            'subtle' => '[:where(&)]:text-zinc-500 dark:[:where(&)]:text-white/70',
            default => '[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white/90',
            'red' => 'text-red-600 dark:text-red-400',
            'orange' => 'text-orange-600 dark:text-orange-400',
            'amber' => 'text-amber-600 dark:text-amber-500',
            'yellow' => 'text-yellow-600 dark:text-yellow-500',
            'lime' => 'text-lime-600 dark:text-lime-500',
            'green' => 'text-green-600 dark:text-green-500',
            'emerald' => 'text-emerald-600 dark:text-emerald-400',
            'teal' => 'text-teal-600 dark:text-teal-400',
            'cyan' => 'text-cyan-600 dark:text-cyan-400',
            'sky' => 'text-sky-600 dark:text-sky-400',
            'blue' => 'text-blue-600 dark:text-blue-400',
            'indigo' => 'text-indigo-600 dark:text-indigo-400',
            'violet' => 'text-violet-600 dark:text-violet-400',
            'purple' => 'text-purple-600 dark:text-purple-400',
            'fuchsia' => 'text-fuchsia-600 dark:text-fuchsia-400',
            'pink' => 'text-pink-600 dark:text-pink-400',
            'rose' => 'text-rose-600 dark:text-rose-400',
        },
        '[&:has(+[data-tallkit-text])]:mb-2 [[data-tallkit-text]+&]:mt-2'
    )"
>
    {{ $slot }}
</tk:element.wrapper>
