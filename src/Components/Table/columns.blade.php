@aware(['verticalLines', 'sticky', 'dense'])

<thead {{ $attributesAfter('head:')->classes([
    '[:where(&)]:bg-zinc-100/80 [:where(&)]:dark:bg-zinc-800/80' => $dense,
    'sticky z-1 top-0 [:where(&)]:bg-white/95 [:where(&)]:dark:bg-zinc-800/95 shadow' => $sticky
]) }}>
    <tr {{ $attributes->whereDoesntStartWith(['head:'])->classes([
        'divide-x divide-zinc-800/10 dark:divide-white/20' => $verticalLines
    ]) }}>
        {{ $slot }}
    </tr>
</thead>
