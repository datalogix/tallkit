@aware(['dense'])

<td {{ $attributes
    ->whereDoesntStartWith(['container:'])
    ->classes([
        'py-4 px-6' => !$dense,
        'p-2.5' => $dense,
        '
            [:where(&)]:bg-white/95 dark:[:where(&)]:bg-zinc-800/95
            z-10
            first:sticky first:left-0 last:sticky last:right-0
            first:after:w-8 first:after:absolute first:after:inset-y-0 first:after:right-0 first:after:translate-x-full first:after:pointer-events-none
            first:after:inset-shadow-[8px_0px_8px_-8px_rgba(0,0,0,0.05)]
            last:after:w-8 last:after:absolute last:after:inset-y-0 last:after:left-0 last:after:-translate-x-full last:after:pointer-events-none
            last:after:inset-shadow-[-8px_0px_8px_-8px_rgba(0,0,0,0.05)]
        ' => $sticky,
        '[:where(&)]:font-normal [:where(&)]:text-sm',
        '[:where(&)]:text-zinc-700 dark:[:where(&)]:text-white/70',
    ])
}}>
    <div {{ $attributesAfter('container:')->classes(
        'flex items-center gap-2',
        match ($align) {
            'center' => 'text-center justify-center',
            'right' => 'text-end justify-end',
            default => 'text-start justify-start',
        })
    }}>
        {{ $slot->isEmpty() ? __($label) : $slot }}
    </div>
</td>
