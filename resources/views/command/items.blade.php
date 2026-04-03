@props([
    'items' => null,
    'size' => null,
])

<ul
    {{
        $attributes->whereDoesntStartWith(['item:'])
        ->classes('overflow-y-auto',
            match ($size) {
                'xs' => '[:where(&)]:max-h-64 [&:has(li:not([data-hidden]))]:p-0.5',
                'sm' => '[:where(&)]:max-h-72 [&:has(li:not([data-hidden]))]:p-1',
                default => '[:where(&)]:max-h-80 [&:has(li:not([data-hidden]))]:p-1',
                'lg' => '[:where(&)]:max-h-88 [&:has(li:not([data-hidden]))]:p-1.5',
                'xl' => '[:where(&)]:max-h-96 [&:has(li:not([data-hidden]))]:p-1.5',
                '2xl' => '[:where(&)]:max-h-104 [&:has(li:not([data-hidden]))]:p-2',
                '3xl' => '[:where(&)]:max-h-112 [&:has(li:not([data-hidden]))]:p-2',
            },
        )
    }}
    role="listbox"
    tabindex="-1"
>
    {{ $slot }}

    @foreach (collect($items) as $item)
        <tk:command.item
            :attributes="TALLKit::attributesAfter($attributes, 'item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
            :$size
        />
    @endforeach
</ul>
