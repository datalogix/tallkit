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
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['errorGroup' => null, 'errorBag' => null, 'focusError' => null, 'clearErrorsOnSubmit' => null, 'errorMessage' => null, 'successMessage' => null, 'submit' => null])->whereDoesntStartWith([
        'card:', 'alert:',
        'image', 'alt', 'icon', 'badge', 'separator', 'content',
        'title:', 'subtitle:', 'container:', 'list:', 'actions:',
    ])"
    :alert="false"
>
    <tk:card
        :attributes="$attributes->prefixed('card:', with: [
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
            :attributes="$attributes->prefixed('alert:')"
            :$size
        >
            {{ $alert ?? '' }}
        </tk:alert.session>

        {{ $slot }}
    </tk:card>
</tk:form>
