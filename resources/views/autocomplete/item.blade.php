@aware(['size'])

<li
    {{ TALLKit::attributesAfter($attributes, 'container:')->classes('w-full group/item data-hidden:hidden') }}
    role="option"
>
    <tk:button
        :attributes="$attributes->whereDoesntStartWith(['container:'])->classes('w-full justify-start')"
        :$size
        variant="ghost"
    >
        {{ $slot }}
    </tk:button>
</li>
