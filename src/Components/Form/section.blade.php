<tk:form
    :attributes="$attributes->whereDoesntStartWith([
        'section:', 'alert:',
        'icon', 'badge', 'separator', 'content',
        'header:', 'container:', 'title:', 'subtitle:', 'list:', 'actions:',
    ])"
    :alert="false"
>
    <tk:section
        :attributes="$attributesAfter('section:', prepend: [
            'icon', 'badge', 'separator', 'content',
            'header:', 'container:', 'title:', 'subtitle:', 'list:', 'actions:',
        ])"
        :$size
        :$prepend
        :$title
        :$subtitle
        :$description
        :$append
        :$actions
    >
        <tk:alert.session
            :attributes="$attributesAfter('alert:')"
            :$size
        >
            {{ $alert ?? '' }}
        </tk:alert.session>

        {{ $slot }}
    </tk:section>
</tk:form>
