@aware(['size'])
@props(['size' => null])
<li
    {{
        TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:')
            ->classes('w-full group/item data-hidden:hidden')
    }}
    id="{{ $attributes->get('wire:key', TALLKit::generateId(prefix: 'listbox-item')) }}"
    role="option"
>
    <tk:button
        :attributes="$attributes->whereDoesntStartWith(['container:'])
            ->classes('w-full justify-start hover:bg-transparent dark:hover:bg-transparent')"
        :$size
        variant="ghost"
        tabindex="-1"
        content:data-item-content
    >
        {{ $slot }}
    </tk:button>
</li>
