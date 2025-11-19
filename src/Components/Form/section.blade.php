<tk:section
    :attributes="$attributesAfter('section:')"
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

    <tk:form :attributes="$attributes->whereDoesntStartWith(['section:', 'alert:'])">
        {{ $slot }}
    </tk:form>

    {{ $append ?? ''}}
</tk:section>
