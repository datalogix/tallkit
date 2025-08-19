@aware(['dense'])

<td {{ $attributes
    ->whereDoesntStartWith(['container:'])
    ->classes([
        'py-4 px-6' => !$dense,
        'p-2.5' => $dense,
        '[:where(&)]:font-normal [:where(&)]:text-sm',
        '[:where(&)]:text-zinc-700 [:where(&)]:dark:text-white/70',
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
