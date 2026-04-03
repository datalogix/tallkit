@aware(['hover', 'stripped', 'verticalLines', 'rowSelection'])

<tr {{ $attributes->classes([
    'data-[state=checked]:bg-zinc-100 dark:data-[state=checked]:bg-zinc-700/50 transition-colors' => $rowSelection,
    'data-[state=checked]:even:bg-white! dark:data-[state=checked]:even:bg-zinc-700/50! transition-colors' => $rowSelection && $stripped,
    'hover:bg-zinc-50 dark:hover:bg-zinc-700/80' => $hover,
    'even:bg-zinc-100 dark:even:bg-zinc-700/50' => $stripped,
    'hover:even:bg-zinc-50! dark:hover:even:bg-zinc-700/80!' => $stripped && $hover,
    'divide-x divide-zinc-800/10 dark:divide-white/20' => $verticalLines,
    'group',
]) }} role="row">
    {{ $slot }}
</tr>
