@aware(['dense', 'rows'])

@php
$href = null;
$action = null;

if ($sortable === true) {
    $sortBy = $name ?? $label;

    if (in_livewire()) {
        $action ??= "sort('$sortBy')";
        $sortable = $this->sortBy === $sortBy ? $this->sortDirection : $sortable;
    } else {
        $sortDirection ??= request('sortBy') === $sortBy ? request('sortDirection') === 'asc' ? 'desc' : 'asc' : 'asc';
        $sortable = request('sortBy') === $sortBy ? request('sortDirection') : $sortable;

        if ($rows instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            $href ??= $rows->withQueryString()->appends(['sortBy' => $sortBy, 'sortDirection' => $sortDirection])->url($rows->currentPage());
        } else if ($rows instanceof \Illuminate\Contracts\Pagination\CursorPaginator) {
            $href ??= $rows->withQueryString()->appends(['sortBy' => $sortBy, 'sortDirection' => $sortDirection])->url($rows->cursor());
        }
    }
}
@endphp

<th {{ $attributes->whereDoesntStartWith(['container:'])->classes([
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
    '[:where(&)]:font-medium [:where(&)]:text-sm',
    '[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white',
]) }}>
    <tk:element
        :attributes="$attributesAfter('container:')->classes(
            'flex w-full group/sortable',
            match ($align) {
                'center' => 'text-center justify-center',
                'right' => 'text-end justify-end',
                default => 'text-start justify-start',
            })
        "
        :label="$label ?? Str::headline($name)"
        :$href
        :$action
        :icon-trailing="match($sortable) {
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
