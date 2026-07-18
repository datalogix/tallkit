@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'resize' => null,
    'rows' => null,
    'maxRows' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel] = TALLKit::resolveFieldContext($attributes, $label);
$hasControl = $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <tk:field.control
        :$size
        :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
            ->when(
                $hasControl,
                fn ($attrs) => $attrs->classes(
                    '
                        flex
                        items-center

                        bg-white
                        dark:bg-white/10

                        border
                        border-zinc-300
                        dark:border-white/10

                        has-[[data-tallkit-control]:disabled]:border-zinc-200
                        dark:has-[[data-tallkit-control]:disabled]:border-white/5

                        has-[[data-tallkit-control][data-invalid]:not(:focus-visible)]:border-red-500
                        dark:has-[[data-tallkit-control][data-invalid]:not(:focus-visible)]:border-red-400

                        has-[[data-tallkit-control][data-invalid]:disabled:not(:focus-visible)]:border-red-500
                        dark:has-[[data-tallkit-control][data-invalid]:disabled:not(:focus-visible)]:border-red-400

                        shadow-xs
                        has-[[data-tallkit-control]:disabled]:shadow-none
                        has-[[data-tallkit-control][data-invalid]:disabled]:shadow-none

                        has-[[data-tallkit-control]:disabled]:opacity-75
                        dark:has-[[data-tallkit-control]:disabled]:opacity-50
                        has-[[data-tallkit-control]:disabled]:cursor-not-allowed

                        has-[[data-tallkit-control]:focus-visible]:outline-2
                        has-[[data-tallkit-control]:focus-visible]:outline-blue-700
                        dark:has-[[data-tallkit-control]:focus-visible]:outline-blue-300
                        has-[[data-tallkit-control]:focus-visible]:outline-offset-0

                        has-[[data-tallkit-control]:focus-visible]:ring-2
                        has-[[data-tallkit-control]:focus-visible]:ring-blue-700/20
                        dark:has-[[data-tallkit-control]:focus-visible]:ring-blue-300/20

                        [&_[data-tallkit-control]]:outline-none
                    ',
                    TALLKit::roundedSize(size: $size, mode: 'large'),
                ),
            )
        "
    >
        <textarea
            x-data="textarea(@js($maxRows))"
            @if ($name) name="{{ $name }}" @endif
            @if ($id) id="{{ $id }}" @endif
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @if ($placeholder) placeholder="{{ __((string) $placeholder) }}" @endif
            @if (is_numeric($rows) || $rows === null) rows="{{ $rows ?? 3 }}" @endif
            {{
                $attributes
                    ->dataKey('textarea')
                    ->dataKey('control')
                    ->dataKey('group-target')
                    ->merge(['wire:model' => $wireModel])
                    ->whereDoesntStartWith([
                        'field:', 'label:', 'info:', 'badge:', 'description:',
                        'group:', 'prefix:', 'suffix:',
                        'help:', 'error:',
                        'control:', 'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                        'textarea:',
                    ])
                    ->except('class')
                    ->classes(['field-sizing-content' => $rows === 'auto'])
                    ->classes(
                        '
                            bg-transparent
                            flex-1
                            peer
                            block
                            w-full
                            appearance-none
                            [print-color-adjust:exact]

                            text-zinc-700
                            disabled:text-zinc-500
                            dark:text-zinc-300
                            dark:disabled:text-zinc-400

                            placeholder-zinc-400
                            disabled:placeholder-zinc-400/70
                            dark:placeholder-zinc-400
                            dark:disabled:placeholder-zinc-500

                            disabled:cursor-not-allowed
                            disabled:resize-none
                        ',
                        TALLKit::fontSize(size: $size, mode: 'large'),
                        TALLKit::paddingBlock(size: $size, mode: 'large'),
                        TALLKit::paddingInline(size: $size, mode: 'large'),
                        TALLKit::paddingStart(size: $size, mode: 'large'),
                        TALLKit::paddingEnd(size: $size, mode: 'large'),
                        match ($maxRows ? 'none' : $resize) {
                            'none' => 'resize-none',
                            'both' => 'resize',
                            'horizontal' => 'resize-x',
                            default => 'resize-y',
                        },
                        $attributes->pluck('textarea:class'),
                    )
                    ->when(
                        !$hasControl,
                        fn ($attrs) => $attrs->classes(
                            '
                                bg-white
                                dark:bg-white/10

                                border
                                border-zinc-300
                                dark:border-white/10

                                disabled:border-zinc-200
                                dark:disabled:border-white/5

                                [&[data-invalid]:not(:focus-visible)]:border-red-500
                                dark:[&[data-invalid]:not(:focus-visible)]:border-red-400

                                disabled:[&[data-invalid]:not(:focus-visible)]:border-red-500
                                dark:disabled:[&[data-invalid]:not(:focus-visible)]:border-red-400

                                shadow-xs
                                disabled:shadow-none
                                [&[data-invalid]]:disabled:shadow-none

                                disabled:opacity-75
                                dark:disabled:opacity-50

                                focus-visible:outline-2
                                focus-visible:outline-blue-700
                                dark:focus-visible:outline-blue-300
                                focus-visible:outline-offset-0

                                focus-visible:ring-2
                                focus-visible:ring-blue-700/20
                                dark:focus-visible:ring-blue-300/20
                            ',
                            TALLKit::roundedSize(size: $size, mode: 'large'),
                        ),
                    )
            }}
        >{{ in_livewire() ? null : ($value ?? $slot) }}</textarea>
    </tk:field.control>
</tk:field.wrapper>
