@props([
    'size' => null,
    'prepend' => null,
    'title' => null,
    'subtitle' => null,
    'description' => null,
    'append' => null,
    'actions' => null,
    'separator' => null,
])
<tk:form
    :attributes="$attributes->whereDoesntStartWith([
        'section:', 'alert:',
        'icon', 'badge', 'separator', 'content',
        'header:', 'container:', 'title:', 'subtitle:', 'list:', 'actions:',
    ])"
    :alert="false"
>
    <tk:section
        :attributes="TALLKit::attributesAfter($attributes, 'section:', prepend: [
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
            :attributes="TALLKit::attributesAfter($attributes, 'alert:')"
            :$size
        >
            {{ $alert ?? '' }}
        </tk:alert.session>

        {{ $slot }}
    </tk:section>
</tk:form>
