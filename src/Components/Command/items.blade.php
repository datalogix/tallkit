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
        <tk:command.item
            :attributes="$attributesAfter('item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
        />
    @endforeach
</ul>
