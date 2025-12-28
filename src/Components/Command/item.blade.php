<li
    {{ $attributesAfter('container:')->classes('w-full group/item data-hidden:hidden') }}
    role="option"
>
    <tk:button
        :attributes="$attributes->whereDoesntStartWith(['container:'])->classes('w-full justify-start')"
        variant="ghost"
    >
        {{ $slot }}
    </tk:button>
</li>
