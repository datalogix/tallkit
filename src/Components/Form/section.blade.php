<tk:section
    :attributes="$attributesAfter('section:')"
    :$title
    :$subtitle
    :$separator
>
    <tk:alert.session :attributes="$attributesAfter('alert:')" />

    {{ $prepend ?? ''}}

    <tk:form :attributes="$attributes->whereDoesntStartWith(['section:', 'alert:'])">
        {{ $slot }}
    </tk:form>

    {{ $append ?? ''}}
</tk:section>
