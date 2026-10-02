@props([
    ...TALLKit::elementProps(),
    'arrow' => null,
    'animate' => null,
])
<tk:button
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->when($animate !== false, fn ($attrs) => $attrs->merge([
        'icon-trailing:class' => 'transition-transform',
        'icon-trailing::class' => '{ \'rotate-180\': opened }',
    ]))"
    ::aria-expanded="opened"
    aria-haspopup="true"
    :iconTrailing="$arrow ?? 'chevron-down'"
/>
