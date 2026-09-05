@aware(['dense'])
@props([
    'name' => null,
    'label' => null,
    'sortable' => null,
    'sortableAction' => null,
    'sortableHref' => null,
    'align' => null,
    'sticky' => null,
])
@php

if ($sortable === true) {
    $sortByColumn = $name ?? $label;

    if (in_livewire()) {
        $sortableAction ??= "sort('$sortByColumn')";
    } else {
        $isCurrentColumn = request('sortBy') === $sortByColumn;
        $requestDirection = request('sortDirection');
        $sortDirection ??= $isCurrentColumn && $requestDirection === 'asc' ? 'desc' : 'asc';
        $sortable = $isCurrentColumn ? ($requestDirection === 'desc' ? 'desc' : 'asc') : $sortable;
        $sortableHref ??= request()->fullUrlWithQuery(['sortBy' => $sortByColumn, 'sortDirection' => $sortDirection]);
    }
}

@endphp
<th
    scope="col"
    {{
        $attributes->whereDoesntStartWith(['container:'])
            ->classes([
                'py-4 px-6' => !$dense,
                'p-2.5' => $dense,
                'tk-table-sticky-column' => $sticky,
                '[:where(&)]:font-medium [:where(&)]:text-sm',
                '[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white',
            ])
            ->merge([
                'aria-sort' => match ($sortable) {
                    'asc' => 'ascending',
                    'desc' => 'descending',
                    true => 'none',
                    default => false,
                }
            ])
    }}
>
    <tk:element
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:')
            ->classes([
                'flex w-full',
                'group/sortable' => $sortable,
                match ($align) {
                    'center' => 'text-center justify-center',
                    'right' => 'text-end justify-end',
                    default => 'text-start justify-start',
                }
            ])
        "
        :label="$label ?? Str::headline($name)"
        :href="$sortable ? $sortableHref : null"
        :action="$sortable ? $sortableAction : null"
        :iconTrailing="match ($sortable) {
            true => 'chevron-up-down',
            'desc' => 'chevron-down',
            'asc' => 'chevron-up',
            default => false
        }"
        icon-trailing:class="opacity-50 group-hover/sortable:opacity-100"
        icon-trailing:size="xs"
    >
        {{ $slot }}
    </tk:element>
</th>
