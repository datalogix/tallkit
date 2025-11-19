<tk:element :attributes="$attributes->classes(
    '
    px-4 whitespace-nowrap
    [:where(&)]:text-zinc-800 dark:[:where(&)]:text-white/85
    [:where(&)]:bg-zinc-800/5 dark:[:where(&)]:bg-white/20
    border-zinc-200 dark:border-white/10
    border-t border-b shadow-xs
    rounded-e-lg
    border-e
    ',
    match ($size) {
        'xs' => 'text-[11px]',
        'sm' => 'text-xs',
        default => 'text-sm',
        'lg' => 'text-base',
        'xl' => 'text-lg',
        '2xl' => 'text-xl',
        '3xl' => 'text-2xl',
    },
)">
    {{ $slot }}
</tk:element>
