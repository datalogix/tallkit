@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'multiple' => null,
    'rows' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext($attributes, $label, $id);
$hasControl = $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');
$options = TALLKit::parseOptions($attributes);

$valueStrings = array_map('strval', Arr::wrap($value));
$optionValues = collect($options)->flatMap(fn ($item, $key) => is_array($item) ? array_keys($item) : [$key])->all();
$hasMatchingOption = (bool) array_intersect($valueStrings, array_map('strval', $optionValues));

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
                    'tk-control-wrapper',
                    TALLKit::roundedSize(size: $size, mode: 'large'),
                    TALLKit::controlFocusRingNested($color),
                ),
            )
        "
    >
        <select
            {{
                $attributes
                    ->dataKey('select')
                    ->dataKey('control')
                    ->dataKey('group-target')
                    ->merge([
                        'name' => $name,
                        'id' => $id,
                        'multiple' => $multiple ? true : null,
                        'size' => $multiple ? ($rows ?? 5) : null,
                        'wire:model' => $wireModel,
                        'aria-describedby' => TALLKit::ariaDescribedBy($id, $description, $help, $invalid, $showError),
                        'aria-invalid' => $invalid ? 'true' : null,
                        'data-invalid' => $invalid ? true : null,
                    ])
                    ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes([
                        'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                        'select:', 'placeholder:', 'optgroup:', 'option:',
                    ]))
                    ->except('class')
                    ->classes(
                        '
                            tk-field-control-base
                            peer

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
                        TALLKit::fontSize(size: $size, mode: 'large'),
                        TALLKit::paddingStart(size: $size, mode: 'large'),
                        TALLKit::paddingEnd(size: $size, mode: 'large'),
                        TALLKit::generateClassBySize(size: $size, name: 'min-h', values: ['8', '9', '10', '12', '14', '16', '18']),
                        $attributes->pluck('select:class'),
                    )
                    ->when(
                        $multiple,
                        fn ($attrs) => $attrs->classes(
                            TALLKit::paddingEnd(size: $size, mode: 'large'),
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
                            'tk-control-standalone',
                            TALLKit::roundedSize(size: $size, mode: 'large'),
                            TALLKit::controlFocusRing($color),
                        ),
                    )
            }}
        >
            @if (($placeholder ?? true) && ! $multiple)
                <tk:select.option
                    :attributes="TALLKit::attributesAfter($attributes, 'placeholder:')->classes('placeholder')"
                    :label="is_string($placeholder) ? $placeholder : '---'"
                    :selected="! $hasMatchingOption"
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
                                    :attributes="TALLKit::attributesAfter($attributes, 'option:')
                                        ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId('select-option', (string) $optionItemGroupValue)] : [], false)
                                    "
                                    :label="$optionItemGroupLabel"
                                    :selected="in_array((string) $optionItemGroupValue, $valueStrings, true)"
                                    :value="$optionItemGroupValue"
                                />
                            @endforeach
                        </optgroup>
                    @else
                        <tk:select.option
                            :attributes="TALLKit::attributesAfter($attributes, 'option:')
                                ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId('select-option', (string) $optionItemValue)] : [], false)
                            "
                            :label="$optionItemLabel"
                            :selected="in_array((string) $optionItemValue, $valueStrings, true)"
                            :value="$optionItemValue"
                        />
                    @endif
                @endforeach
            @endif
        </select>
    </tk:field.control>
</tk:field.wrapper>
