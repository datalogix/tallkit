@php
$hasRowExpanded = isset($expanded) || isset($rowExpanded) || Str::contains($slot, 'role="row-expanded"', true);
$hasRowSelection = $rowSelection || Str::contains($slot, 'role="row-selection"', true) || $selectAll;
$colspan = $cols->count() + ($hasRowSelection ? 1 : 0) + ($hasRowExpanded ? 1 : 0);
@endphp

<div {{
    $attributesAfter('container:')
        ->classes([
            'overflow-hidden',
            'border border-zinc-800/10 dark:border-white/20 rounded-md' => $dense || $border
        ])
        ->merge($hasRowSelection || $hasRowExpanded ? ['x-data' => 'table'] : [])
}}>
    <div {{ $attributesAfter('area:')->classes('overflow-x-auto') }}>
        <table {{
            $attributes
                ->whereDoesntStartWith([
                    'container:', 'area:',
                    'columns:', 'thead:', 'select-all:', 'column:', 'column-', 'th:', 'th-',
                    'rows:', 'tbody:', 'row:', 'row-', 'cell:', 'cell-', 'td:', 'td-',
                    'no-records:', 'footer:', 'tfoot:',
                    'pagination:',
                ])
                ->classes('
                    relative
                    [:where(&)]:min-w-full
                    text-zinc-800
                    divide-y divide-zinc-800/10 dark:divide-white/20
                    whitespace-nowrap',
                )
        }}>
            @if (Str::doesntContain($slot, '<thead', true) && $cols->isNotEmpty())
                <tk:table.columns :attributes="$attributesAfter('columns:')->merge($attributesAfter('thead:')->toArray())">
                    @if ($hasRowExpanded)
                        <tk:table.column.expanded />
                    @endif

                    @if ($hasRowSelection)
                        <tk:table.column.select-all :attributes="$attributesAfter('select-all:')">
                            @if ($isSlot($selectAll))
                                {{ $selectAll}}
                            @elseif ($selectAll === false)
                                &nbsp;
                            @endif
                        </tk:table.column.select-all>
                    @endif

                    @foreach ($cols as $key => $col)
                        <tk:table.column :attributes="$attributesAfter('column:')
                            ->merge($attributesAfter('column-'.$key.':')->toArray())
                            ->merge($attributesAfter('th:')->toArray())
                            ->merge($attributesAfter('th-'.$key.':')->toArray())
                            ->merge(Arr::wrap(data_forget($col, '_key')))
                            ->classes(['w-0' => $key === 'actions'])
                        ">
                            @isset (${'col_' . $key})
                                {{ ${'col_' . $key}($col, $key, data_get($col, 'name', $key), $cols, $rows) }}
                            @endisset
                        </tk:table.column>
                    @endforeach
                </tk:table.columns>
            @endif

            @if (Str::doesntContain($slot, '<tbody', true) && $cols->isNotEmpty() && ($pagination === false || $rows->isNotEmpty()))
                <tk:table.rows :attributes="$attributesAfter('rows:')->merge($attributesAfter('tbody:')->toArray())">
                    @forelse ($rows as $index => $row)
                        <tk:table.row
                            data-id="{{ data_get($row, $rowKey ?? 'id', $index) }}"
                            :attributes="$attributesAfter('row:')
                                ->merge($hasRowSelection ? ['data-state' => 'unchecked'] : [])
                                ->merge($hasRowExpanded ? ['data-expanded' => 'close'] : [])
                                ->merge(in_livewire() ? ['wire:key' => data_get($row, $rowKey ?? 'id', $index)] : [], false)
                            "
                        >
                            @if ($hasRowExpanded)
                                <tk:table.cell.expanded :attributes="$attributesAfter('cell-expanded:')">
                                    {{ $cellExpanded ?? '' }}
                                </tk:table.cell.expanded>
                            @endif

                            @if ($hasRowSelection)
                                <tk:table.cell.selection :attributes="$attributesAfter('cell-selection:')">
                                    @if ($isSlot($rowSelection))
                                        {{ $rowSelection}}
                                    @endif
                                </tk:table.cell.selection>
                            @endif

                            @foreach ($cols as $key => $col)
                                <tk:table.cell :attributes="$attributesAfter('cell:')
                                    ->merge($attributesAfter('cell-'.$key.':')->toArray())
                                    ->merge($attributesAfter('td:')->toArray())
                                    ->merge($attributesAfter('td-'.$key.':')->toArray())
                                    ->merge(['align' => data_get($col, 'align', $key === 'actions' ? 'center' : null)])
                                    ->merge(['sticky' => data_get($col, 'sticky')])
                                ">
                                    @isset (${'row_' . $key})
                                        {{ ${'row_' . $key}($row, $key, $getRowValue($row, $key, $col), $col, $cols, $rows) }}
                                    @else
                                        {!! $getRowValue($row, $key, $col)() !!}
                                    @endif
                                </tk:table.cell>
                            @endforeach
                        </tk:table.row>

                        @if ($hasRowExpanded)
                            <tk:table.row.expanded :attributes="$attributesAfter('row-expanded:')->merge(['colspan' => $colspan])">
                                @if (isset($expanded))
                                    {{ $expanded($row, $cols, $rows) }}
                                @elseif (isset($rowExpanded))
                                     {{ $rowExpanded($row, $cols, $rows) }}
                                @endif
                            </tk:table.row.expanded>
                        @endif
                    @empty
                        @if ($pagination === false)
                            <tk:table.row.no-records :attributes="$attributesAfter('no-records:')->merge(['colspan' => $colspan])">
                                {{ $noRecords }}
                            </tk:table.row.no-records>
                        @endif
                    @endforelse
                </tk:table.rows>
            @endif

            @if (Str::doesntContain($slot, '<tfoot', true) && isset($footer))
                <tk:table.footer :attributes="$attributesAfter('footer:')
                    ->merge($attributesAfter('tfoot:')->toArray())
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
            :attributes="$attributesAfter('pagination:')"
            :paginator="$rows"
        />
    @endif
</div>
