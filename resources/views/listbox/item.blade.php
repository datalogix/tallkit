@aware(['size'])
@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'label' => null,
])
<li
    {{
        $attributes->prefixed('container:')
            ->classes('w-full group/item data-hidden:hidden cursor-pointer aria-disabled:cursor-default')
    }}
    id="{{ $attributes->get('wire:key') ?? TALLKit::stableId('listbox-item') }}"
    role="option"
>
    <tk:button
        :attributes="$attributes->withElementProps(get_defined_vars())->except('type')->whereDoesntStartWith(['container:'])
            ->classes('w-full justify-start pointer-events-none [&[disabled]]:opacity-disabled')"
        variant="ghost"
        :$size
        :$label
        as="div"
        :type="false"
        content:data-item-content
    >
        {{ $slot }}
    </tk:button>
</li>
