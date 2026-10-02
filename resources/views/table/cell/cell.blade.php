@aware(['dense', 'toggleable' => null, 'pinnable' => null])
@props([
    'align' => null,
    'label' => null,
    'sticky' => null,
    'column' => null,
    'pinnable' => null,
    'hidden' => null,
])
@php

$columnAttributes = TALLKit::tableColumnAttributes(
    name: $column !== null && $column !== '' ? (string) $column : null,
    toggleable: (bool) $toggleable,
    hidden: (bool) $hidden,
    sticky: $sticky,
    pinnable: (bool) $pinnable && $column !== null && $column !== '',
);

@endphp
<td {{ $attributes
    ->whereDoesntStartWith(['container:'])
    ->classes([
        'py-4 px-6' => ! $dense,
        'p-2.5' => $dense,
        'tk-table-sticky-column' => $sticky && $sticky !== 'left',
        '
            [:where(&)]:font-normal [:where(&)]:text-sm
        ',
        TALLKit::textNeutral(prefix: '[:where(&)]:'),
    ])
    ->mergeDefined($columnAttributes, escape: false)
}}>
    <div {{ $attributes->prefixed('container:')->classes(
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
