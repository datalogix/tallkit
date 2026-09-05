@aware(['size'])
@props([
    'size' => null,
    'color' => null,
    'value' => null,
    'href' => null,
    'prepend' => null,
    'append' => null,
    'actions' => null,
])
<tk:element
    name="status"
    :$href
    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:')
        ->classes(
            '
                justify-center
                text-center

                transition
                duration-300
                [print-color-adjust:exact]
            ',
            TALLKit::roundedSize(size: $size, mode: 'large'),
            TALLKit::padding(size: $size, mode: 'largest'),
            TALLKit::mutedBackground(color: $color) ?? 'bg-zinc-400/20 dark:bg-zinc-400/40 [&:is(a)]:hover:bg-zinc-400/30 dark:[&:is(a)]:hover:bg-zinc-400/50',
        )
    "
>
    <tk:content
        :attributes="$attributes->whereDoesntStartWith(['container:', 'value:'])->classes('flex-col justify-center items-center')"
        :$size
        :$append
        :$actions
    >
        <x-slot:prepend>
            {{ $prepend }}

            <tk:heading
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'value:')
                    ->classes(
                        'block w-full',
                        match ($size) {
                            'xs' => 'text-lg',
                            'sm' => 'text-xl',
                            default => 'text-2xl',
                            'lg' => 'text-3xl',
                            'xl' => 'text-4xl',
                            '2xl' => 'text-5xl',
                            '3xl' => 'text-6xl',
                        }
                    )
                "
                :variant="$color"
                :label="$value"
            />
        </x-slot:prepend>

        {{ $slot }}
    </tk:content>
</tk:element>
