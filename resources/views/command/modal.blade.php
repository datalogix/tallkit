@props([
    'size' => null,
    'shortcut' => null,
    'focusOnOpen' => null,
    'closeOnSelect' => null,
])

<tk:modal
    :attributes="TALLKit::attributesAfter($attributes, 'modal:')
        ->classes('fixed mt-20 mx-auto')
        ->merge($focusOnOpen !== false ? ['x-on:opened' => '$el.querySelector(\'[data-tallkit-command-input]\')?.focus()'] : [])
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
            ->merge($closeOnSelect !== false ? ['x-on:command-item-selected' => 'close'] : [])
        "
        :$size
    >
        {{ $content ?? '' }}

        @isset ($input)
            <x-slot:input>
                {{ $input }}
            </x-slot:input>
        @endisset

        @isset ($empty)
            <x-slot:empty>
                {{ $empty }}
            </x-slot:empty>
        @endisset
    </tk:command>
</tk:modal>
