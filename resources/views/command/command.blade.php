@props([
    'items' => null,
    'size' => null,
    'searchable' => null,
    'noRecords' => null,
])

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
                TALLKit::roundedSize(size: $size, mode: 'large')
            )
    }}
>
    @isset ($input)
        {{ $input }}
    @elseif ($searchable !== false && Str::of($slot)->doesntContain('data-tallkit-command-input'))
        <tk:command.input
            :attributes="TALLKit::attributesAfter($attributes, 'input:')"
            :$size
        />
    @endisset

    @if (Str::of($slot)->contains('data-tallkit-command-items'))
        {{ $slot }}
    @else
        <tk:command.items
            :attributes="TALLKit::attributesAfter($attributes, 'items:')"
            :$items
            :$size
        >
            {{ $slot }}
        </tk:command.items>
    @endif

    @isset ($empty)
        {{ $empty }}
    @elseif ($noRecords !== false && Str::of($slot)->doesntContain('data-tallkit-command-no-records'))
        <tk:command.no-records
            :attributes="TALLKit::attributesAfter($attributes, 'no-records:')"
            :$size
        />
    @endisset
</div>
