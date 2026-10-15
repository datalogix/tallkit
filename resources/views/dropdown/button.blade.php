@props([
    ...TALLKit::elementProps(),
    'arrow' => null,
    'animate' => null,
])
<tk:button
    :attributes="$attributes->withElementProps(get_defined_vars())->when($animate !== false, fn ($attrs) => $attrs->merge([
        'icon-trailing:class' => 'transition-transform',
        'icon-trailing::class' => '{ \'rotate-180\': opened }',
    ]))"
    ::aria-expanded="opened"
    aria-haspopup="true"
    :iconTrailing="$arrow ?? 'chevron-down'"
/>
