<tk:card
    :attributes="$attributesAfter('card:')"
    :$title
    :$subtitle
    :$separator
>
    <tk:alert.session :attributes="$attributesAfter('alert:')" />

    {{ $prepend ?? ''}}

    <tk:form :attributes="$attributes->whereDoesntStartWith(['card:', 'alert:'])">
        {{ $slot }}
    </tk:form>

    {{ $append ?? ''}}
</tk:card>
