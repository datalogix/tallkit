@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @php
    $content = Str::of($slot)->trim();
    @endphp
    <div
        x-data="command"
        {{
            $attributes->whereDoesntStartWith(['input:', 'items:',' no-records:'])->classes('
                bg-white dark:bg-zinc-700
                [:where(&)]:w-md block rounded-xl overflow-hidden shadow-xs
                border border-zinc-200 dark:border-zinc-600
            ')
        }}
    >
        @isset ($input)
            {{ $input }}
        @elseif ($searchable !== false && $content->doesntContain($dataKey('input')))
            <tk:command.input :attributes="$attributesAfter('input:')" />
        @endisset

        @if ($content->contains($dataKey('items')))
            {{ $slot }}
        @else
            <tk:command.items
                :attributes="$attributesAfter('items:')"
                :$items
            >
                {{ $slot }}
            </tk:command.items>
        @endif

        @isset ($empty)
            {{ $empty }}
        @elseif ($noRecords !== false && $content->doesntContain($dataKey('no-records')))
            <tk:command.no-records :attributes="$attributesAfter('no-records:')" />
        @endisset
    </div>
@endif
