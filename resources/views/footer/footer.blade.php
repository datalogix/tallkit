@props([
    'sticky' => null,
    'container' => null,
    'variant' => null,
])
<footer {{
    $attributes
        ->dataKey('footer')
        ->whereDoesntStartWith(['container:'])
        ->classes([
            '[grid-area:footer]',
            'p-6 lg:p-8' => ! $container,
            'sticky bottom-0 z-10 shadow-xs border-t border-current/15' => $sticky,
            TALLKit::frameClasses($variant),
        ])
    }}
>
    <tk:container.wrapper
        :attributes="$attributes->prefixed('container:')->classes('p-6 lg:p-8')"
        :$container
    >
        {{ $slot }}
    </tk:container.wrapper>
</footer>
