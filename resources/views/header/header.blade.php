@props([
    'sticky' => null,
    'container' => null,
    'variant' => null,
])
<header
    {{
        $attributes
            ->dataKey('header')
            ->whereDoesntStartWith(['container:'])
            ->mergeDefined(['x-data' => $sticky ? 'header' : null, 'wire:ignore.self' => $sticky ? true : null])
            ->classes('[grid-area:header] z-10 min-h-16')
            ->classes([
                'flex items-center px-4 lg:px-6' => ! $container,
                'shadow-xs border-b border-current/15' => $sticky,
            ])
            ->classes(TALLKit::frameClasses($variant))
    }}
>
    <tk:container.wrapper
        :attributes="$attributes->prefixed('container:')->classes('min-h-16 h-full flex items-center')"
        :$container
    >
        {{ $slot }}
    </tk:container.wrapper>
</header>
