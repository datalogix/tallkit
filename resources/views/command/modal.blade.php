@props([
    'size' => null,
    'shortcut' => null,
    'focusOnOpen' => null,
    'closeOnSelect' => null,
])
<tk:modal
    :attributes="TALLKit::attributesAfter($attributes, 'modal:')
        ->classes('fixed mt-20 mx-auto')
        ->merge($focusOnOpen !== false ? ['x-on:opened' => '$el.querySelector(\'[data-tallkit-input]\')?.focus()'] : [])
    "
    variant="bare"
    :$size
    :$shortcut
>
    <x-slot:trigger>
        @if ($slot->isEmpty())
            <tk:button
                :attributes="TALLKit::attributesAfter($attributes, 'trigger:')"
                :$size
                label="Search"
                icon="search"
                variant="filled"
            />
        @else
            {{ $slot }}
        @endif
    </x-slot:trigger>

    <tk:command
        :attributes="$attributes->whereDoesntStartWith(['trigger:', 'modal:'])
            ->classes('[:where(&)]:w-md')
            ->merge($closeOnSelect !== false ? ['x-on:listbox-item-selected' => 'close'] : [])
        "
        :$size
    >
        {{ $content ?? '' }}

        @isset ($search)
            <x-slot:search>
                {{ $search }}
            </x-slot:search>
        @endisset

        @isset ($empty)
            <x-slot:empty>
                {{ $empty }}
            </x-slot:empty>
        @endisset
    </tk:command>
</tk:modal>
