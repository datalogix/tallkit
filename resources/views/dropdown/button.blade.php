@props([
    'arrow' => null,
    'animate' => null,
])
<tk:button
    :attributes="$attributes->when($animate !== false, fn ($attrs) => $attrs->merge([
        'icon-trailing:class' => 'transition-transform',
        'icon-trailing::class' => '{ \'rotate-180\': opened }',
    ]))"
    ::aria-expanded="opened"
    aria-haspopup="true"
    :iconTrailing="$arrow ?? 'chevron-down'"
/>
