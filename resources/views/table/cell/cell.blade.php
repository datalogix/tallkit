@aware(['dense'])
@props([
    'align' => null,
    'label' => null,
    'sticky' => null,
])
<td {{ $attributes
    ->whereDoesntStartWith(['container:'])
    ->classes([
        'py-4 px-6' => !$dense,
        'p-2.5' => $dense,
        'tk-table-sticky-column' => $sticky,
        '
            [:where(&)]:font-normal [:where(&)]:text-sm
            [:where(&)]:text-zinc-700 dark:[:where(&)]:text-white/70
        ',
    ])
}}>
    <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:')->classes(
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
