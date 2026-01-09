<tk:card
    :attributes="$attributesAfter('card:')
        ->merge($attributesAfter('icon', prepend: true)->getAttributes())
        ->merge($attributesAfter('badge', prepend: true)->getAttributes())
    "
    :$title
    :$subtitle
    :$separator
    :$size
>
    @isset ($header)
        <x-slot:header>
            {{ $header }}
        </x-slot:header>
    @endisset

    @isset ($description)
        <x-slot:description>
            {{ $description }}
        </x-slot:description>
    @endisset

    @isset ($actions)
        <x-slot:actions :attributes="$actions->attributes">
            {{ $actions }}
        </x-slot:actions>
    @endisset

    <tk:alert.session
        :attributes="$attributesAfter('alert:')"
        :$size
    >
        {{ $alert ?? '' }}
    </tk:alert.session>

    {{ $prepend ?? ''}}

    <tk:form
        :attributes="$attributes->whereDoesntStartWith(['card:', 'icon', 'badge', 'alert:'])"
        :alert="false"
    >
        {{ $slot }}
    </tk:form>

    {{ $append ?? ''}}
</tk:card>
