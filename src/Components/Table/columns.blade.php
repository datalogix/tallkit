@aware(['verticalLines', 'sticky', 'dense'])

<thead {{ $attributesAfter('head:')->classes([
    '[:where(&)]:bg-zinc-100/80 dark:[:where(&)]:bg-zinc-800/80' => $dense,
    'sticky z-20 top-0 [:where(&)]:bg-white/95 dark:[:where(&)]:bg-zinc-800/95 shadow' => $sticky,
    '[&>tr]:divide-x [&>tr]:divide-zinc-800/10 [&>tr]:dark:divide-white/20' => $verticalLines,
    '[&>tr]:border-b [&>tr]:border-zinc-800/10 [&>tr]:dark:border-white/20',
]) }}>
    @if (Str::doesntContain($slot, '<tr', true))
        <tr {{ $attributes->whereDoesntStartWith(['head:']) }}>
            {{ $slot }}
        </tr>
    @else
        {{ $slot }}
    @endif
</thead>
