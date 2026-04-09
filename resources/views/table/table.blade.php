@props([
    'resource' => null,
    'rows' => null,
    'cols' => null,
    'pagination' => null,
    'sortable' => null,
    'border' => null,
    'dense' => null,
    'stripped' => null,
    'hover' => null,
    'verticalLines' => null,
    'horizontalLines' => null,
    'sticky' => null,
    'rowSelection' => null,
    'selectAll' => null,
    'rowKey' => null,
    'noRecords' => null,
    'footer' => null,
    'displayIdColumn' => null,
    'mapRelationsColumn' => null,
])
@php

$cols = collect($cols);
$rows ??= is_string($resource) ? make_model($resource) : $resource;
$rows = match (true) {
    $rows instanceof Model => $rows->paginate(),
    $rows instanceof Builder => $rows->paginate(),
    $rows instanceof Relation => $rows->paginate(),
    $rows instanceof Paginator => $rows,
    $rows instanceof CursorPaginator => $rows,
    $rows === null => null,
    default => collect($rows),
};

if ($cols->isEmpty() && $rows?->isNotEmpty()) {
    $cols = collect($rows->first())->keys();
} elseif ($displayIdColumn === null) {
    $displayIdColumn = true;
}

$cols = $cols->filter()
    ->mapWithKeys(function ($value, $key) use ($sortable) {
        $name = data_get($value, 'name', is_array($value) ? $key : $value);
        $newKey = Str::snake(is_numeric($key) ? $name : $key);

        return [
            $newKey => [
                '_key' => $key,
                'sortable' => data_get($value, 'sortable', $name !== 'actions' && $sortable),
                'name' => Str::before($name, '.'),
            ] + (is_array($value) ? $value : []),
        ];
    })
    ->unless($displayIdColumn, fn ($cols) => $cols->filter(fn ($col, $key) => mb_strtolower($key) !== 'id'))
    ->when($mapRelationsColumn ?? true, function ($cols) {
         $mappedRelations = [];

        return $cols->mapWithKeys(function ($col, $key) use (&$mappedRelations) {
            if (in_array($key, $mappedRelations)) {
                return null;
            }

            if (Str::endsWith($key, '_id')) {
                $mappedRelations[] = $col;

                return [Str::replaceLast('_id', '', $key) => $col];
            }

            return [$key => $col];
        });
    });

$hasRowExpanded = isset($expanded) || isset($rowExpanded) || Str::contains($slot, 'role="row-expanded"', true);
$hasRowSelection = $rowSelection || Str::contains($slot, 'role="row-selection"', true) || $selectAll;
$colspan = $cols->count() + ($hasRowSelection ? 1 : 0) + ($hasRowExpanded ? 1 : 0);

@endphp
<div {{
    TALLKit::attributesAfter($attributes, 'container:')
        ->classes([
            'overflow-hidden',
            'border border-zinc-800/10 dark:border-white/20 rounded-md' => $dense || $border
        ])
        ->merge($hasRowSelection || $hasRowExpanded ? ['x-data' => 'table'] : [])
}}>
    <div {{ TALLKit::attributesAfter($attributes, 'area:')->classes('overflow-x-auto') }}>
        <table {{
            $attributes
                ->whereDoesntStartWith([
                    'container:', 'area:',
                    'columns:', 'thead:', 'select-all:', 'column:', 'column-', 'th:', 'th-',
                    'rows:', 'tbody:', 'row:', 'row-', 'cell:', 'cell-', 'td:', 'td-',
                    'no-records:', 'footer:', 'tfoot:',
                    'pagination:',
                ])
                ->classes(
                    '
                        relative
                        [:where(&)]:min-w-full
                        text-zinc-800
                        divide-y divide-zinc-800/10 dark:divide-white/20
                        whitespace-nowrap
                    ',
                )
        }}>
            @if (Str::doesntContain($slot, '<thead', true) && $cols->isNotEmpty())
                <tk:table.columns :attributes="TALLKit::attributesAfter($attributes, 'columns:')->merge(TALLKit::attributesAfter($attributes, 'thead:')->toArray())">
                    @if ($hasRowExpanded)
                        <tk:table.column.expanded />
                    @endif

                    @if ($hasRowSelection)
                        <tk:table.column.select-all :attributes="TALLKit::attributesAfter($attributes, 'select-all:')">
                            @if (TALLKit::isSlot($selectAll))
                                {{ $selectAll}}
                            @elseif ($selectAll === false)
                                &nbsp;
                            @endif
                        </tk:table.column.select-all>
                    @endif

                    @foreach ($cols as $key => $col)
                        <tk:table.column :attributes="TALLKit::attributesAfter($attributes, 'column:')
                            ->merge(TALLKit::attributesAfter($attributes, 'column-'.$key.':')->toArray())
                            ->merge(TALLKit::attributesAfter($attributes, 'th:')->toArray())
                            ->merge(TALLKit::attributesAfter($attributes, 'th-'.$key.':')->toArray())
                            ->merge(Arr::wrap(data_forget($col, '_key')))
                            ->classes(['w-0' => $key === 'actions'])
                        ">
                            @isset (${'col_' . $key})
                                {{ ${'col_' . $key}(col: $col, key: $key, name: data_get($col, 'name', $key), cols: $cols, rows: $rows) }}
                            @endisset
                        </tk:table.column>
                    @endforeach
                </tk:table.columns>
            @endif

            @if (Str::doesntContain($slot, '<tbody', true) && $cols->isNotEmpty() && ($pagination === false || $rows->isNotEmpty()))
                <tk:table.rows :attributes="TALLKit::attributesAfter($attributes, 'rows:')->merge(TALLKit::attributesAfter($attributes, 'tbody:')->toArray())">
                    @forelse ($rows as $index => $row)
                        <tk:table.row
                            data-id="{{ data_get($row, $rowKey ?? 'id', $index) }}"
                            :attributes="TALLKit::attributesAfter($attributes, 'row:')
                                ->merge($hasRowSelection ? ['data-state' => 'unchecked'] : [])
                                ->merge($hasRowExpanded ? ['data-expanded' => 'close'] : [])
                                ->merge(in_livewire() ? ['wire:key' => data_get($row, $rowKey ?? 'id', $index)] : [], false)
                            "
                        >
                            @if ($hasRowExpanded)
                                <tk:table.cell.expanded :attributes="TALLKit::attributesAfter($attributes, 'cell-expanded:')">
                                    {{ $cellExpanded ?? '' }}
                                </tk:table.cell.expanded>
                            @endif

                            @if ($hasRowSelection)
                                <tk:table.cell.selection :attributes="TALLKit::attributesAfter($attributes, 'cell-selection:')">
                                    @if (TALLKit::isSlot($rowSelection))
                                        {{ $rowSelection}}
                                    @endif
                                </tk:table.cell.selection>
                            @endif

                            @foreach ($cols as $key => $col)
                                <tk:table.cell :attributes="TALLKit::attributesAfter($attributes, 'cell:')
                                    ->merge(TALLKit::attributesAfter($attributes, 'cell-'.$key.':')->toArray())
                                    ->merge(TALLKit::attributesAfter($attributes, 'td:')->toArray())
                                    ->merge(TALLKit::attributesAfter($attributes, 'td-'.$key.':')->toArray())
                                    ->merge(['align' => data_get($col, 'align', $key === 'actions' ? 'center' : null)])
                                    ->merge(['sticky' => data_get($col, 'sticky')])
                                ">
                                    @if (isset(${'row_' . $key}))
                                        {{ ${'row_' . $key}(
                                            row: $row,
                                            key: $key,
                                            value: fn () => TALLKit::getRowValue($row, $key, $col),
                                            col: $col,
                                            cols: $cols,
                                            rows: $rows,
                                            index: $index
                                        ) }}
                                    @elseif ($key == 'row_index')
                                        {{ $index + 1 }}
                                    @else
                                        @php
                                        $rowValue = TALLKit::getRowValue($row, $key, $col);
                                        @endphp

                                        @if (is_bool($rowValue))
                                            <tk:icon :name="$rowValue === true ? 'check' : 'times'" />
                                        @else
                                            {!! $rowValue !!}
                                        @endif
                                    @endif
                                </tk:table.cell>
                            @endforeach
                        </tk:table.row>

                        @if ($hasRowExpanded)
                            <tk:table.row.expanded :attributes="TALLKit::attributesAfter($attributes, 'row-expanded:')
                                ->merge(['colspan' => $colspan])
                            ">
                                @if (isset($expanded))
                                    {{ $expanded($row, $cols, $rows) }}
                                @elseif (isset($rowExpanded))
                                    {{ $rowExpanded($row, $cols, $rows) }}
                                @endif
                            </tk:table.row.expanded>
                        @endif
                    @empty
                        @if ($pagination === false)
                            <tk:table.row.no-records :attributes="TALLKit::attributesAfter($attributes, 'no-records:')
                                ->merge(['colspan' => $colspan])
                            ">
                                {{ $noRecords }}
                            </tk:table.row.no-records>
                        @endif
                    @endforelse
                </tk:table.rows>
            @endif

            @if (Str::doesntContain($slot, '<tfoot', true) && isset($footer))
                <tk:table.footer :attributes="TALLKit::attributesAfter($attributes, 'footer:')
                    ->merge(TALLKit::attributesAfter($attributes, 'tfoot:')->toArray())
                    ->merge(['cell:colspan' => $colspan])
                ">
                    {{ $footer }}
                </tk:table.footer>
            @endif

            {{ $slot }}
        </table>
    </div>

    @if ($pagination !== false && $rows !== null)
        <tk:pagination
            :attributes="TALLKit::attributesAfter($attributes, 'pagination:')"
            :paginator="$rows"
        />
    @endif
</div>
