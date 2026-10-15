@props([
    ...TALLKit::elementProps(),
    'label' => 'Send',
])
<tk:button
    :attributes="$attributes->withElementProps(get_defined_vars())"
    type="submit"
>
    {{ $slot }}
</tk:button>
