<div {{ $attributesAfter('container:') }}>
    <div {{ $attributesAfter('area:')->classes('overflow-x-auto') }}>
        <table
            data-tallkit-table
            {{
                $attributes->classes('[:where(&)]:min-w-full
                    text-zinc-800
                    divide-y
                    divide-zinc-800/10
                    dark:divide-white/20
                    text-zinc-800
                    whitespace-nowrap'
                )
                ->classes($hover ? 'hover' : '')
                ->classes($stripped ? 'stripped' : '')
            }}
        >
            @if (!Str::contains($slot, 'thead') && $cols->isNotEmpty())
                <tk:table.columns>
                    @foreach ($cols as $key => $col)
                        <tk:table.column>
                            @isset(${'col_'.$key})
                                {{ ${'col_'.$key}($col, $key, $cols, $rows) }}
                            @else
                                {!! __(Str::headline(data_get($col, 'name', $key))) !!}
                            @endif
                        </tk:table.column>
                    @endforeach
                </tk:table.columns>
            @endif

            @if (!Str::contains($slot, 'tbody') && $cols->isNotEmpty())
                <tk:table.rows>
                    @forelse ($rows as $row)
                        <tk:table.row>
                            @foreach ($cols as $key => $col)
                                <tk:table.cell>
                                    @isset(${'row_'.$key})
                                        {{ ${'row_'.$key}($row, $key, $col, $cols, $rows) }}
                                    @else
                                        {!! $getRowValue($row, $key) !!}
                                    @endif
                                </tk:table.cell>
                            @endforeach
                        </tk:table.row>
                    @empty
                        <tk:table.row>
                            <tk:table.cell
                                :attributes="$attributesAfter('empty:')"
                                colspan="{{ $cols->count() }}"
                            >
                                {!! $empty ?: __('No records found') !!}
                            </tk:table.cell>
                        </tk:table.row>
                    @endforelse
                </tk:table.rows>
            @endif

            {{ $slot }}
        </table>
    </div>
</div>
