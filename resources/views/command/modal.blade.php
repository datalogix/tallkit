@props([
    'name' => null,
    'size' => null,
    'shortcut' => null,
    'focusOnOpen' => null,
    'closeOnSelect' => null,
])
<tk:modal
    :attributes="$attributes->prefixed('modal:')
        ->classes('fixed mt-20 mx-auto')
        ->mergeDefined(['x-on:opened' => $focusOnOpen !== false ? '$el.querySelector(\''.TALLKit::dataSelector('input').'\')?.focus()' : null])
    "
    variant="bare"
    :$name
    :$size
    :$shortcut
    aria-label="{{ __('Search') }}"
>
    <x-slot:trigger>
        @if ($slot->isEmpty())
            <tk:button
                :attributes="$attributes->prefixed('trigger:')->merge(['label' => 'Search'])"
                :$size
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
            ->mergeDefined(['x-on:selected' => $closeOnSelect !== false ? 'close' : null])
        "
        :$size
        search:x-on:keydown.escape.prevent="close"
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
