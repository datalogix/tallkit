@props([
    'sticky' => null,
    'container' => null,
    'variant' => null,
])
<footer {{
    $attributes
        ->dataKey('footer')
        ->whereDoesntStartWith(['container:'])
        ->classes([
            '[grid-area:footer]',
            'p-6 lg:p-8' => !$container,
            'sticky bottom-0 z-10 shadow border-t border-current/15' => $sticky,
            match ($variant) {
                'none' => '',
                'accent' => 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)]',
                'inverse' => 'bg-zinc-800 dark:bg-white [:where(&)]:text-white/85 dark:[:where(&)]:text-zinc-800/85',
                'strong' => 'bg-zinc-700/15 dark:bg-black [:where(&)]:text-zinc-900 dark:[:where(&)]:text-white',
                'subtle' => 'bg-zinc-700/5 dark:bg-zinc-800/50 [:where(&)]:text-zinc-700/70 dark:[:where(&)]:text-white/70',
                'ghost' => 'bg-transparent [:where(&)]:text-zinc-800/85 dark:[:where(&)]:text-white/85',
                default => TALLKit::frameBackground(color: $variant) ?? 'bg-zinc-700/10 dark:bg-zinc-800 [:where(&)]:text-zinc-800/85 dark:[:where(&)]:text-white/85',
            }
        ])
    }}
>
    <tk:container.wrapper
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:')->classes('p-6 lg:p-8')"
        :$container
    >
        {{ $slot }}
    </tk:container.wrapper>
</footer>
