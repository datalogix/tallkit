
<header {{
    $attributes
        ->whereDoesntStartWith(['container:'])
        ->merge($sticky ? ['x-data' => 'header'] : [])
        ->classes('[grid-area:header] z-10 min-h-16')
        ->classes([
            'flex items-center px-4 lg:px-6' => !$container,
            'shadow border-b border-current/15' => $sticky,
        ])
        ->classes(match ($variant) {
            'none' => '',
            'accent' => 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)]',
            'inverse' => 'bg-zinc-800 dark:bg-white [:where(&)]:text-white/85 dark:[:where(&)]:text-zinc-800/85',
            'strong' => 'bg-zinc-700/15 dark:bg-black [:where(&)]:text-zinc-900 dark:[:where(&)]:text-white',
            'subtle' => 'bg-zinc-700/5 dark:bg-zinc-800/50 [:where(&)]:text-zinc-700/70 dark:[:where(&)]:text-white/70',
            'ghost' => 'bg-transparent [:where(&)]:text-zinc-800/85 dark:[:where(&)]:text-white/85',
            default => 'bg-zinc-700/10 dark:bg-zinc-800 [:where(&)]:text-zinc-800/85 dark:[:where(&)]:text-white/85',
            'red' => 'bg-red-700 dark:bg-red-600 *:text-white',
            'orange' => 'bg-orange-700 dark:bg-orange-600 *:text-white',
            'amber' => 'bg-amber-700 dark:bg-amber-600 *:text-white',
            'yellow' => 'bg-yellow-700 dark:bg-yellow-600 *:text-white',
            'lime' => 'bg-lime-700 dark:bg-lime-600 *:text-white',
            'green' => 'bg-green-700 dark:bg-green-600 *:text-white',
            'emerald' => 'bg-emerald-700 dark:bg-emerald-600 *:text-white',
            'teal' => 'bg-teal-700 dark:bg-teal-600 *:text-white',
            'cyan' => 'bg-cyan-700 dark:bg-cyan-600 *:text-white',
            'sky' => 'bg-sky-700 dark:bg-sky-600 *:text-white',
            'blue' => 'bg-blue-700 dark:bg-blue-600 *:text-white',
            'indigo' => 'bg-indigo-700 dark:bg-indigo-600 *:text-white',
            'violet' => 'bg-violet-700 dark:bg-violet-600 *:text-white',
            'purple' => 'bg-purple-700 dark:bg-purple-600 *:text-white',
            'fuchsia' => 'bg-fuchsia-700 dark:bg-fuchsia-600 *:text-white',
            'pink' => 'bg-pink-700 dark:bg-pink-600 *:text-white',
            'rose' => 'bg-rose-700 dark:bg-rose-600 *:text-white',
        })
}}>
     <tk:container.wrapper
        :attributes="$attributesAfter('container:')->classes('min-h-16 h-full flex items-center')"
        :$container
    >
        {{ $slot }}
    </tk:container.wrapper>
</header>
