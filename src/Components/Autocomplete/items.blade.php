@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    <ul
        {{
            $attributes->whereDoesntStartWith(['item:'])
            ->classes(
                '
                    [&:has(li:not([data-hidden]))]:p-1
                    overflow-y-auto
                    [:where(&)]:max-h-80
                '
            )
        }}
        role="listbox"
        tabindex="-1"
    >
        {{ $slot }}

        @foreach (collect($items) as $item)
            <tk:autocomplete.item
                :attributes="$attributesAfter('item:')->merge($item, false)"
            />
        @endforeach
    </ul>
@endif
