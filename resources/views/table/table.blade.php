@props([
    'resource' => null,
    'rows' => null,
    'cols' => null,
    'pagination' => null,
    'sortable' => null,
    'border' => null,
    'dense' => null,
    'striped' => null,
    'hover' => null,
    'verticalLines' => null,
    'horizontalLines' => null,
    'sticky' => null,
    'draggable' => null,
    'resizable' => null,
    'toggleable' => null,
    'pinnable' => null,
    'persist' => null,
    'columnMinWidth' => null,
    'columnMaxMinWidth' => null,
    'rowSelection' => null,
    'selectAll' => null,
    'rowKey' => null,
    'noRecords' => null,
    'footer' => null,
    'idColumn' => null,
    'mapRelationsColumn' => null,
    'expanded' => null,
    'rowExpanded' => null,
])
@php

// Out of the attributes: they print on the table, and a Closure can't be printed.
$attributes = $attributes->filter(fn ($value, $key) => ! (str_starts_with($key, 'row_') && $value instanceof \Closure));

// The tag is built, not written (see table/columns.blade.php).
$slotHas = fn (string $tag) => Str::contains($slot, '<'.$tag, true);
$cols = collect($cols);
$rows ??= is_string($resource) ? make_model($resource) : $resource;

// Full class names: a compiled view has no use statements.
$query = match (true) {
    $rows instanceof \Illuminate\Database\Eloquent\Model => $rows->newQuery(),
    $rows instanceof \Illuminate\Database\Eloquent\Builder,
    $rows instanceof \Illuminate\Database\Query\Builder,
    $rows instanceof \Illuminate\Database\Eloquent\Relations\Relation => $rows,
    default => null,
};

$rows = match (true) {
    $query !== null => ($sortable && ! $query instanceof \Illuminate\Database\Query\Builder ? TALLKit::tableSortQuery($query, $cols) : $query)->paginate(),
    $rows instanceof \Illuminate\Contracts\Pagination\Paginator => $rows,
    $rows instanceof \Illuminate\Contracts\Pagination\CursorPaginator => $rows,
    $rows === null => null,
    default => collect($rows),
};

if ($cols->isEmpty() && ($rows?->isNotEmpty() || $query !== null && ! $query instanceof \Illuminate\Database\Query\Builder)) {
    // Never a secret: only what the model shows ($visible, else its key, $fillable and timestamps).
    $cols = collect(TALLKit::tableDefaultColumns($rows?->first() ?? $query->getModel()));
} elseif ($idColumn === null) {
    $idColumn = true;
}

$cols = $cols->filter()
    ->mapWithKeys(function ($value, $key) use ($sortable) {
        $labelled = ! is_array($value) && ! is_numeric($key);
        $name = data_get($value, 'name', is_array($value) || $labelled ? $key : $value);
        $newKey = Str::snake(is_numeric($key) ? $name : $key);

        return [
            $newKey => [
                '_key' => $key,
                'sortable' => data_get($value, 'sortable', $name !== 'actions' && $sortable),
                'name' => Str::before($name, '.'),
            ] + (is_array($value) ? $value : ($labelled ? ['label' => $value] : [])),
        ];
    })
    ->unless($idColumn, fn ($cols) => $cols->filter(fn ($col, $key) => mb_strtolower($key) !== 'id'))
    ->when($mapRelationsColumn ?? true, function ($cols) {
        $taken = [];

        return $cols->mapWithKeys(function ($col, $key) use (&$taken) {
            $key = Str::endsWith($key, '_id') ? Str::replaceLast('_id', '', $key) : $key;

            if (isset($taken[$key])) {
                return [];
            }

            $taken[$key] = true;

            return [$key => $col];
        });
    });

$hasRowExpanded = isset($expanded) || isset($rowExpanded) || Str::contains($slot, 'data-role="row-expanded"', true);
$hasRowSelection = $rowSelection || Str::contains($slot, 'data-role="row-selection"', true) || $selectAll;
$colspan = max(1, $cols->count() + ($hasRowSelection ? 1 : 0) + ($hasRowExpanded ? 1 : 0));

$hasPinnedColumns = Str::contains($slot, 'data-column-sticky', true) || Str::contains($slot, 'data-column-pinnable', true) || $cols->contains(fn ($col) => data_get($col, 'sticky') === 'left');
$hasColumnFeatures = $draggable || $resizable || $toggleable || $pinnable || $hasPinnedColumns;

$tableData = $hasRowSelection || $hasRowExpanded || $hasColumnFeatures
    ? ['x-data' => 'table('.Js::from(array_filter([
        'draggable' => (bool) $draggable,
        'resizable' => (bool) $resizable,
        'toggleable' => (bool) $toggleable,
        'pinnable' => (bool) $pinnable,
        'persist' => $persist,
        'minColumnWidth' => $columnMinWidth,
        'maxMinColumnWidth' => $columnMaxMinWidth,
    ], fn ($value) => $value !== null && $value !== false)).')']
    : [];

$tableAttributes = array_filter([
    'x-bind:style' => $resizable || $pinnable || $hasPinnedColumns ? 'columnTableStyle()' : null,
    'x-bind:data-column-resized' => $resizable ? 'columnResizeActive()' : null,
]);

@endphp
<div {{
    $attributes->prefixed('container:')
        ->dataKey('table-container')
        ->classes([
            'overflow-hidden',
            'border border-zinc-800/10 dark:border-white/20 rounded-md' => $dense || $border
        ])
        ->merge($tableData)
}}>
    <div {{ $attributes->prefixed('area:')->classes('overflow-x-auto') }}>
        <table {{
            $attributes
                ->whereDoesntStartWith([
                    'container:', 'area:',
                    'columns:', 'select-all:', 'column:', 'column-',
                    'rows:', 'row:', 'row-', 'cell:', 'cell-',
                    'no-records:', 'footer:',
                    'pagination:',
                ])
                ->merge($tableAttributes, escape: false)
                ->classes(
                    '
                        relative
                        [:where(&)]:min-w-full
                        divide-y divide-zinc-800/10 dark:divide-white/20
                        whitespace-nowrap
                    ',
                    TALLKit::textNeutral(variant: 'strong'),
                )
        }}>
            {{ $slot }}

            @if (! $slotHas('thead') && $cols->isNotEmpty())
                @if ($resizable)
                    <tk:table.colgroup>
                        @if ($hasRowExpanded)
                            <tk:table.col fixed="expand" />
                        @endif

                        @if ($hasRowSelection)
                            <tk:table.col fixed="select" />
                        @endif

                        @foreach ($cols as $key => $col)
                            <tk:table.col :name="$key" :hidden="data_get($col, 'hidden')" />
                        @endforeach
                    </tk:table.colgroup>
                @endif

                <tk:table.columns :attributes="$attributes->prefixed('columns:')">
                    @if ($hasRowExpanded)
                        <tk:table.column.expanded />
                    @endif

                    @if ($hasRowSelection)
                        <tk:table.column.select-all :attributes="$attributes->prefixed('select-all:')">
                            @if (TALLKit::isSlot(slot: $selectAll))
                                {{ $selectAll}}
                            @elseif ($selectAll === false)
                                &nbsp;
                            @endif
                        </tk:table.column.select-all>
                    @endif

                    @foreach ($cols as $key => $col)
                        {{-- As props: merge() escapes, and they'd be escaped twice. --}}
                        <tk:table.column :attributes="TALLKit::attributesMerge(
                                $attributes->prefixed('column:'),
                                $attributes->prefixed('column-'.$key.':'),
                            )
                            ->merge(Arr::except(Arr::wrap(data_forget($col, '_key')), ['label', 'name']))
                            ->merge(['data-column-key' => $key])
                            ->classes(['w-0' => $key === 'actions'])
                        "
                            :label="data_get($col, 'label')"
                            :name="data_get($col, 'name')"
                        >
                            @isset (${'col_' . $key})
                                {{ ${'col_' . $key}(col: $col, key: $key, name: data_get($col, 'name', $key), cols: $cols, rows: $rows) }}
                            @endisset
                        </tk:table.column>
                    @endforeach
                </tk:table.columns>
            @endif

            @if (! $slotHas('tbody') && ($cols->isNotEmpty() || $rows?->isEmpty()))
                @php
                    $cellShells = [];
                    $cellParams = [];
                @endphp
                <tk:table.rows :attributes="$attributes->prefixed('rows:')">
                    @forelse ($rows as $index => $row)
                        <tk:table.row
                            data-id="{{ data_get($row, $rowKey ?? 'id', $index) }}"
                            :attributes="$attributes->prefixed('row:')
                                ->mergeDefined(['data-state' => $hasRowSelection ? 'unchecked' : null])
                                ->mergeDefined(['data-expanded' => $hasRowExpanded ? 'close' : null])
                                ->wireKey(data_get($row, $rowKey ?? 'id', $index))
                            "
                        >
                            @if ($hasRowExpanded)
                                <tk:table.cell.expanded :attributes="$attributes->prefixed('cell-expanded:')">
                                    {{ $cellExpanded ?? '' }}
                                </tk:table.cell.expanded>
                            @endif

                            @if ($hasRowSelection)
                                <tk:table.cell.selection :attributes="$attributes->prefixed('cell-selection:')">
                                    @if (TALLKit::isSlot(slot: $rowSelection))
                                        {{ $rowSelection}}
                                    @endif
                                </tk:table.cell.selection>
                            @endif

                            @foreach ($cols as $key => $col)
                                @php
                                    ob_start();
                                @endphp
                                    @php
                                        $cellRenderer = ${'row_'.$key} ?? ${Str::camel('row_'.$key)} ?? null;

                                        // Only the parameters it declares: an unknown named one fails.
                                        if ($cellRenderer instanceof \Closure) {
                                            $cellParams[$key] ??= collect((new \ReflectionFunction($cellRenderer))->getParameters())
                                                ->mapWithKeys(fn ($parameter) => [$parameter->getName() => $parameter->isVariadic()])
                                                ->all();

                                            $cellArguments = [
                                                'row' => $row,
                                                'key' => $key,
                                                'value' => fn () => TALLKit::tableCellValue(row: $row, key: $key, col: $col),
                                                'col' => $col,
                                                'cols' => $cols,
                                                'rows' => $rows,
                                                'index' => $index,
                                            ];
                                        }
                                    @endphp
                                    @if ($cellRenderer instanceof \Closure)
                                        {{ $cellRenderer(...(in_array(true, $cellParams[$key], true) ? $cellArguments : array_intersect_key($cellArguments, $cellParams[$key]))) }}
                                    @elseif ($cellRenderer !== null)
                                        {{ $cellRenderer }}
                                    @elseif ($key == 'row_index')
                                        {{ $index + 1 }}
                                    @else
                                        @php
                                        $rowValue = TALLKit::tableCellValue(row: $row, key: $key, col: $col);
                                        @endphp

                                        @if (is_bool($rowValue))
                                            <span class="sr-only">{{ $rowValue === true ? __('Yes') : __('No') }}</span>
                                            <tk:icon :icon="$rowValue === true ? 'check' : 'close'" />
                                        @else
                                            {!! $rowValue !!}
                                        @endif
                                    @endif
                                @php
                                    $cellContent = ob_get_clean();
                                @endphp

                                @if (trim($cellContent) === '')
                                    <tk:table.cell :attributes="TALLKit::attributesMerge(
                                            $attributes->prefixed('cell:'),
                                            $attributes->prefixed('cell-'.$key.':'),
                                        )
                                        ->merge(['align' => data_get($col, 'align', $key === 'actions' ? 'center' : null)])
                                        ->merge(['sticky' => data_get($col, 'sticky')])
                                        ->merge(['column' => $key, 'hidden' => data_get($col, 'hidden')])
                                    " />
                                @elseif (isset($cellShells[$key]))
                                    {!! $cellShells[$key][0].$cellContent.$cellShells[$key][1] !!}
                                @else
                                    @php
                                        ob_start();
                                    @endphp
                                    <tk:table.cell :attributes="TALLKit::attributesMerge(
                                            $attributes->prefixed('cell:'),
                                            $attributes->prefixed('cell-'.$key.':'),
                                        )
                                        ->merge(['align' => data_get($col, 'align', $key === 'actions' ? 'center' : null)])
                                        ->merge(['sticky' => data_get($col, 'sticky')])
                                        ->merge(['column' => $key, 'hidden' => data_get($col, 'hidden')])
                                    ">__tallkit_cell_content__</tk:table.cell>
                                    @php
                                        $cellShells[$key] = explode('__tallkit_cell_content__', ob_get_clean(), 2);
                                    @endphp
                                    {!! $cellShells[$key][0].$cellContent.$cellShells[$key][1] !!}
                                @endif
                            @endforeach
                        </tk:table.row>

                        @if ($hasRowExpanded)
                            <tk:table.row.expanded :attributes="$attributes->prefixed('row-expanded:')
                                ->merge(['colspan' => $colspan])
                                ->mergeDefined(['x-bind:colspan' => $resizable ? 'columnColspan()' : null])
                            ">
                                @php
                                    $expandedContent = $expanded ?? $rowExpanded;
                                @endphp
                                @if ($expandedContent instanceof \Closure)
                                    {{ $expandedContent($row, $cols, $rows) }}
                                @elseif ($expandedContent !== null)
                                    {{ $expandedContent }}
                                @endif
                            </tk:table.row.expanded>
                        @endif
                    @empty
                        <tk:table.row.no-records :attributes="$attributes->prefixed('no-records:')
                            ->merge(['colspan' => $colspan])
                            ->mergeDefined(['x-bind:colspan' => $resizable ? 'columnColspan()' : null])
                        ">
                            {{ $noRecords }}
                        </tk:table.row.no-records>
                    @endforelse
                </tk:table.rows>
            @endif

            @if (! $slotHas('tfoot') && isset($footer))
                <tk:table.footer :attributes="$attributes->prefixed('footer:')
                    ->merge(['cell:colspan' => $colspan])
                ">
                    {{ $footer }}
                </tk:table.footer>
            @endif
        </table>
    </div>

    @if ($pagination !== false && $rows !== null)
        <tk:pagination
            :attributes="$attributes->prefixed('pagination:')"
            :paginator="$rows"
        />
    @endif
</div>
