<tk:element :attributes="$attributes->classes(
    '
    px-4 whitespace-nowrap
    [:where(&)]:text-zinc-800 dark:[:where(&)]:text-white/85
    [:where(&)]:bg-zinc-800/5 dark:[:where(&)]:bg-white/20
    border-zinc-200 dark:border-white/10
    border-t border-b shadow-xs
    rounded-s-lg
    border-s
    ',
    $fontSize($size),
)">
    {{ $slot }}
</tk:element>
