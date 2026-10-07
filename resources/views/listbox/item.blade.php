@aware(['size'])
{{-- The label as a prop: left in the attributes, it would be escaped twice. --}}
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
    {{-- Counted only without a key: get()'s default is evaluated either way and would use up a number. --}}
    id="{{ $attributes->get('wire:key') ?? TALLKit::stableId('listbox-item') }}"
    role="option"
>
    {{-- No pointer events: the list highlights the option under the mouse, and a CSS hover would keep a second one lit while the keyboard moves. --}}
    <tk:button
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->whereDoesntStartWith(['container:'])
            ->merge(['variant' => 'ghost'])
            ->classes('w-full justify-start pointer-events-none [&[disabled]]:opacity-disabled')"
        :$size
        :$label
        as="div"
        :type="false"
        content:data-item-content
    >
        {{ $slot }}
    </tk:button>
</li>
