<tk:form
    :attributes="$attributes->whereDoesntStartWith([
        'card:', 'alert:',
        'image', 'alt', 'icon', 'badge', 'separator', 'content',
        'title:', 'subtitle:', 'container:', 'list:', 'actions:',
    ])"
    :alert="false"
>
    <tk:card
        :attributes="$attributesAfter('card:', prepend: [
            'image', 'alt', 'icon', 'badge', 'separator', 'content',
            'title:', 'subtitle:', 'container:', 'list:', 'actions:',
        ])"
        :$size
        :$header
        :$prepend
        :$title
        :$subtitle
        :$description
        :$append
        :$actions
        :$footer
    >
        <tk:alert.session
            :attributes="$attributesAfter('alert:')"
            :$size
        >
            {{ $alert ?? '' }}
        </tk:alert.session>

        {{ $slot }}
    </tk:card>
</tk:form>
