<tk:section
    :attributes="$attributesAfter('section:')
        ->merge($attributesAfter('icon', prepend: true)->getAttributes())
        ->merge($attributesAfter('badge', prepend: true)->getAttributes())
    "
    :$title
    :$subtitle
    :$separator
    :$size
>
    @isset ($description)
        <x-slot:description>
            {{ $description }}
        </x-slot:description>
    @endisset

    @isset ($actions)
        <x-slot:actions>
            {{ $actions }}
        </x-slot:actions>
    @endisset

    <tk:alert.session
        :attributes="$attributesAfter('alert:')"
        :$size
    >
        {{ $alert ?? '' }}
    </tk:alert.session>

    {{ $prepend ?? ''}}

    <tk:form :attributes="$attributes->whereDoesntStartWith(['section:', 'icon', 'badge', 'alert:'])">
        {{ $slot }}
    </tk:form>

    {{ $append ?? ''}}
</tk:section>
