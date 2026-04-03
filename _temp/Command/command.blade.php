<div
    x-data="command"
    {{
        $attributes->whereDoesntStartWith(['input:', 'items:',' no-records:'])
            ->classes(
                '
                    bg-white dark:bg-zinc-700
                    [:where(&)]:w-md block overflow-hidden shadow-xs
                    border border-zinc-200 dark:border-zinc-600
                ',
                $roundedSize(size: $size, mode: 'large')
            )
    }}
>
    @isset ($input)
        {{ $input }}
    @elseif ($searchable !== false && Str::of($slot)->doesntContain($dataKey('input')))
        <tk:command.input
            :attributes="$attributesAfter('input:')"
            :$size
        />
    @endisset

    @if (Str::of($slot)->contains($dataKey('items')))
        {{ $slot }}
    @else
        <tk:command.items
            :attributes="$attributesAfter('items:')"
            :$items
            :$size
        >
            {{ $slot }}
        </tk:command.items>
    @endif

    @isset ($empty)
        {{ $empty }}
    @elseif ($noRecords !== false && Str::of($slot)->doesntContain($dataKey('no-records')))
        <tk:command.no-records
            :attributes="$attributesAfter('no-records:')"
            :$size
        />
    @endisset
</div>
