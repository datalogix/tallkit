<tk:card
    :attributes="$attributesAfter('card:')"
    :$title
    :$subtitle
    :$separator
    :$size
>
    <tk:alert.session
        :attributes="$attributesAfter('alert:')"
        :$size
    >
        {{ $alert ?? '' }}
    </tk:alert.session>

    {{ $prepend ?? ''}}

    <tk:form :attributes="$attributes->whereDoesntStartWith(['card:', 'alert:'])">
        {{ $slot }}
    </tk:form>

    {{ $append ?? ''}}
</tk:card>
