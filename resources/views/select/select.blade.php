@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'multiple' => null,
    'rows' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel] = TALLKit::resolveFieldContext($attributes, $label);
$hasControl = $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');
$options = TALLKit::parseOptions($attributes);

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
                    match ($size) {
                        'xs' => 'rounded-md',
                        'sm' => 'rounded-md',
                        default => 'rounded-lg',
                        'lg' => 'rounded-lg',
                        'xl' => 'rounded-lg',
                        '2xl' => 'rounded-xl',
                        '3xl' => 'rounded-xl',
                    },
                ),
            )
        "
    >
        <select
            @if ($name) name="{{ $name }}" @endif
            @if ($id) id="{{ $id }}" @endif
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @if ($multiple) multiple size="{{ $rows ?? 5 }}" @endif
            {{
                $attributes
                    ->dataKey('select')
                    ->dataKey('control')
                    ->dataKey('group-target')
                    ->merge(['wire:model' => $wireModel])
                    ->whereDoesntStartWith([
                        'field:', 'label:', 'info:', 'badge:', 'description:',
                        'group:', 'prefix:', 'suffix:',
                        'help:', 'error:',
                        'control:', 'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                        'select:', 'placeholder:', 'optgroup:', 'option:',
                    ])
                    ->except('class')
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

                            truncate
                            has-[option.placeholder:checked]:text-zinc-400
                            dark:has-[option.placeholder:checked]:text-zinc-400
                            dark:[&_option]:bg-zinc-700
                            dark:[&_*]:text-white

                            bg-size-[1.5em_1.5em]
                            bg-no-repeat

                            bg-position-[right_.5rem_center]
                            rtl:bg-position-[left_.5rem_center]
                        ',
                        match ($size) {
                            'xs' => 'min-h-8 text-xs ps-2 pe-2',
                            'sm' => 'min-h-9 text-sm ps-2.5 pe-2.5',
                            default => 'min-h-10 text-base ps-3 pe-3',
                            'lg' => 'min-h-12 text-lg ps-3.5 pe-3.5',
                            'xl' => 'min-h-14 text-xl ps-4 pe-4',
                            '2xl' => 'min-h-16 text-2xl ps-4.5 pe-4.5',
                            '3xl' => 'min-h-18 text-3xl ps-5 pe-5',
                        },
                        $attributes->pluck('select:class'),
                    )
                    ->when(
                        $multiple,
                        fn ($attrs) => $attrs->classes(
                            'overflow-auto bg-none',
                            match ($size) {
                                'xs' => 'py-2 [&_*]:py-1 space-y-1 [&_option]:px-2 [&_option]:rounded-md [&>optgroup>option]:ms-2 [&>optgroup]:space-y-1 [&>optgroup>option:first-child]:mt-1 [&>optgroup>option:last-child]:mb-1',
                                'sm' => 'py-2.5 [&_*]:py-1 space-y-1 [&_option]:px-2.5 [&_option]:rounded-md [&>optgroup>option]:ms-2.5 [&>optgroup]:space-y-1 [&>optgroup>option:first-child]:mt-1 [&>optgroup>option:last-child]:mb-1',
                                default => 'py-3 [&_*]:py-1.5 space-y-1.5 [&_option]:px-3 [&_option]:rounded-lg [&>optgroup>option]:ms-3 [&>optgroup]:space-y-1.5 [&>optgroup>option:first-child]:mt-1.5 [&>optgroup>option:last-child]:mb-1.5',
                                'lg' => 'py-3.5 [&_*]:py-1.5 space-y-1.5 [&_option]:px-3.5 [&_option]:rounded-lg [&>optgroup>option]:ms-3.5 [&>optgroup]:space-y-1.5 [&>optgroup>option:first-child]:mt-1.5 [&>optgroup>option:last-child]:mb-1.5',
                                'xl' => 'py-4 [&_*]:py-2 space-y-2 [&_option]:px-4 [&_option]:rounded-lg [&>optgroup>option]:ms-4 [&>optgroup]:space-y-2 [&>optgroup>option:first-child]:mt-2 [&>optgroup>option:last-child]:mb-2',
                                '2xl' => 'py-4.5 [&_*]:py-2 space-y-2 [&_option]:px-4.5 [&_option]:rounded-xl [&>optgroup>option]:ms-4.5 [&>optgroup]:space-y-2 [&>optgroup>option:first-child]:mt-2 [&>optgroup>option:last-child]:mb-2',
                                '3xl' => 'py-5 [&_*]:py-2.5 space-y-2.5 [&_option]:px-4 [&_option]:rounded-xl [&>optgroup>option]:ms-5 [&>optgroup]:space-y-2.5 [&>optgroup>option:first-child]:mt-2.5 [&>optgroup>option:last-child]:mb-2.5',
                            },
                        ),
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
                            match ($size) {
                                'xs' => 'rounded-md',
                                'sm' => 'rounded-md',
                                default => 'rounded-lg',
                                'lg' => 'rounded-lg',
                                'xl' => 'rounded-lg',
                                '2xl' => 'rounded-xl',
                                '3xl' => 'rounded-xl',
                            },
                        ),
                    )
            }}
        >
            @if ($placeholder ?? true && ! $multiple)
                <tk:select.option
                    :attributes="TALLKit::attributesAfter($attributes, 'placeholder:')->classes('placeholder')"
                    :label="is_string($placeholder) ? $placeholder : '---'"
                    :selected="true"
                    :value="''"
                />
            @endif

            @if ($slot->hasActualContent())
                {{ $slot }}
            @else
                @foreach ($options as $optionItemValue => $optionItemLabel)
                    @if (is_array($optionItemLabel))
                        <optgroup
                            {{ TALLKit::attributesAfter($attributes, 'optgroup:') }}
                            label="{{ __($optionItemValue ?: '---') }}"
                        >
                            @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                                <tk:select.option
                                    :attributes="TALLKit::attributesAfter($attributes, 'option:')"
                                    :label="$optionItemGroupLabel"
                                    :selected="in_array($optionItemGroupValue, Arr::wrap($value))"
                                    :value="$optionItemGroupValue"
                                />
                            @endforeach
                        </optgroup>
                    @else
                        <tk:select.option
                            :attributes="TALLKit::attributesAfter($attributes, 'option:')"
                            :label="$optionItemLabel"
                            :selected="in_array($optionItemValue, Arr::wrap($value))"
                            :value="$optionItemValue"
                        />
                    @endif
                @endforeach
            @endif
        </select>
    </tk:field.control>
</tk:field.wrapper>
