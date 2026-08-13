@aware(['dense', 'rows'])
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
        $sortDirection ??= request('sortBy') === $sortByColumn ? request('sortDirection') === 'asc' ? 'desc' : 'asc' : 'asc';
        $sortable = request('sortBy') === $sortByColumn ? (request('sortDirection') === 'desc' ? 'desc' : 'asc') : $sortable;

        if ($rows instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            $sortableHref ??= $rows->withQueryString()->appends(['sortBy' => $sortByColumn, 'sortDirection' => $sortDirection])->url($rows->currentPage());
        } else if ($rows instanceof \Illuminate\Contracts\Pagination\CursorPaginator) {
            $sortableHref ??= $rows->withQueryString()->appends(['sortBy' => $sortByColumn, 'sortDirection' => $sortDirection])->url($rows->cursor());
        }
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
    }}
>
    <tk:element
        :attributes="TALLKit::attributesAfter($attributes, 'container:')
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
