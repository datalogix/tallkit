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
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['errorGroup' => null, 'errorBag' => null, 'focusError' => null, 'clearErrorsOnSubmit' => null, 'errorMessage' => null, 'successMessage' => null, 'submit' => null])->whereDoesntStartWith([
        'section:', 'alert:',
        'icon', 'badge', 'separator', 'content',
        'header:', 'container:', 'title:', 'subtitle:', 'list:', 'actions:',
    ])"
    :alert="false"
>
    <tk:section
        :attributes="$attributes->prefixed('section:', with: [
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
        :$separator
    >
        <tk:alert.session
            :attributes="$attributes->prefixed('alert:')"
            :$size
        >
            {{ $alert ?? '' }}
        </tk:alert.session>

        {{ $slot }}
    </tk:section>
</tk:form>
