<tk:modal
    :attributes="$attributesAfter('modal:')
        ->classes('fixed mt-20 mx-auto')
        ->merge($focusOnOpen !== false ? ['x-on:opened' => '$el.querySelector(\'[data-tallkit-command-input]\')?.focus()'] : [])
    "
    variant="bare"
    :$shortcut
>
    <x-slot:trigger>
        @if ($slot->isEmpty())
            <tk:button
                :attributes="$attributesAfter('trigger:')"
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
