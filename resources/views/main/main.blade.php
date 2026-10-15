@props([
    'container' => null,
    'id' => null,
])
<main
    id="{{ $id ?? 'main-content' }}"
    tabindex="-1"
    {{
        $attributes->dataKey('main')->classes([
            '[grid-area:main] p-6 lg:p-8 [[data-tallkit-container]_&]:px-0',
            'mx-auto w-full' => $container,
            TALLKit::containerWidth($container) => $container,
        ])
    }}
>
    {{ $slot }}
</main>
