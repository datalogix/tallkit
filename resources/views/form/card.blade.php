@props([
    'size' => null,
    'header' => null,
    'prepend' => null,
    'title' => null,
    'subtitle' => null,
    'description' => null,
    'append' => null,
    'actions' => null,
    'footer' => null,
])
<tk:form
    :attributes="$attributes->whereDoesntStartWith([
        'card:', 'alert:',
        'image', 'alt', 'icon', 'badge', 'separator', 'content',
        'title:', 'subtitle:', 'container:', 'list:', 'actions:',
    ])"
    :alert="false"
>
    <tk:card
        :attributes="TALLKit::attributesAfter($attributes, 'card:', prepend: [
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
            :attributes="TALLKit::attributesAfter($attributes, 'alert:')"
            :$size
        >
            {{ $alert ?? '' }}
        </tk:alert.session>

        {{ $slot }}
    </tk:card>
</tk:form>
