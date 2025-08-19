@php $invalid ??= $name && $errors->has($name); @endphp

<tk:field.wrapper :$attributes>
    <select
        {{ $buildDataAttribute('control') }}
        {{ $buildDataAttribute('group-target') }}
        @isset ($name) name="{{ $name }}" @endisset
        @isset ($id) id="{{ $id }}" @endisset
        @if ($invalid) aria-invalid="true" data-invalid @endif
        @if ($multiple) multiple size="{{ $rows ?? 5 }}" @endif
        {{ $attributes->whereDoesntStartWith([
                'field:', 'label:', 'information:', 'badge:', 'description:', 'help:', 'error:',
                'group:', 'prefix:', 'suffix:',
            ])
            ->when(
                $multiple,
                fn ($attrs) => $attrs->classes(
                    'ps-3 pe-3',
                    match ($size) {
                        'xs' => '[&>option]:px-1 [&_*]:py-px [&_*]:rounded-sm space-y-px [&>optgroup]:space-y-px [&>optgroup>option:first-child]:mt-px [&>optgroup>option:last-child]:mb-px',
                        'sm' => '[&>option]:px-1.5 [&_*]:py-1 [&_*]:rounded-md space-y-1 [&>optgroup]:space-y-1 [&>optgroup>option:first-child]:mt-px [&>optgroup>option:last-child]:mb-px',
                        default => '[&>option]:px-2 [&_*]:py-1.5 [&_*]:rounded-lg space-y-1 [&>optgroup]:space-y-1 [&>optgroup>option:first-child]:mt-1 [&>optgroup>option:last-child]:mb-1',
                        'lg' => '[&>option]:px-2 [&_*]:py-1.5 [&_*]:rounded-lg space-y-1.5 [&>optgroup]:space-y-1.5 [&>optgroup>option:first-child]:mt-1 [&>optgroup>option:last-child]:mb-1',
                        'xl' => '[&>option]:px-2.5 [&_*]:py-2 [&_*]:rounded-xl space-y-1.5 [&>optgroup]:space-y-1.5 [&>optgroup>option:first-child]:mt-1.5 [&>optgroup>option:last-child]:mb-1.5',
                        '2xl' => '[&>option]:px-3 [&_*]:py-2.5 [&_*]:rounded-xl space-y-2 [&>optgroup]:space-y-2 [&>optgroup>option:first-child]:mt-2 [&>optgroup>option:last-child]:mb-2',
                        '3xl' => '[&>option]:px-3.5 [&_*]:py-3 [&_*]:rounded-2xl space-y-2.5 [&>optgroup]:space-y-2.5 [&>optgroup>option:first-child]:mt-2.5 [&>optgroup>option:last-child]:mb-2.5',
                    }
                ),
                fn ($attrs) => $attrs->classes('ps-3 pe-10')
            )
            ->classes(
                match ($size) {
                    'xs' => 'min-h-8 py-1.5 text-xs rounded-md',
                    'sm' => 'min-h-10 py-2 text-sm rounded-md',
                    default => 'min-h-12 py-2.5 text-base rounded-lg',
                    'lg' => 'min-h-14 py-3 text-lg rounded-lg',
                    'xl' => 'min-h-16 py-3.5 text-xl rounded-xl',
                    '2xl' => 'min-h-18 py-4 text-2xl rounded-xl',
                    '3xl' => 'min-h-20 py-4.5 text-3xl rounded-2xl',
                },
                '
                    peer
                    block
                    w-full
                    appearance-none
                    truncate

                    shadow-xs
                    disabled:shadow-none

                    border
                    border-zinc-300 dark:border-white/10
                    disabled:border-zinc-200 dark:disabled:border-white/5
                    [&[data-invalid]]:border-red-500 dark:[&[data-invalid]]:border-red-400

                    text-zinc-700
                    disabled:text-zinc-500

                    placeholder-zinc-400
                    disabled:placeholder-zinc-400/70

                    dark:text-zinc-300
                    dark:disabled:text-zinc-400
                    dark:placeholder-zinc-400
                    dark:disabled:placeholder-zinc-500

                    bg-white
                    dark:bg-white/10
                    disabled:opacity-50

                    focus:ring-blue-500
                    focus:border-blue-500

                    has-[option.placeholder:checked]:text-zinc-400 dark:has-[option.placeholder:checked]:text-zinc-400
                    dark:[&_*]:bg-zinc-700 dark:[&_*]:text-white
                    dark:[&_*:checked]:bg-zinc-600 dark:[&_*:checked)]:text-white

                    bg-size-[1.5em_1.5em]
                    bg-no-repeat
                    [print-color-adjust:exact]

                    bg-position-[right_.5rem_center]
                    rtl:bg-position-[left_.5rem_center]
                ',
            )
        }}
    >
        @if ($placeholder ?? true && ! $multiple)
            <tk:select.option
                :label="is_string($placeholder) ? $placeholder : '---'"
                :selected="true"
                :value="''"
                class="placeholder"
            />
        @endif

        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            @foreach ($options as $optionItemValue => $optionItemLabel)
                @if (is_array($optionItemLabel))
                    <optgroup label="{{ __($optionItemValue ?: '---') }}">
                        @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                            <tk:select.option
                                :label="$optionItemGroupLabel"
                                :selected="in_array($optionItemGroupValue, $value)"
                                :value="$optionItemGroupValue"
                            />
                        @endforeach
                    </optgroup>
                @else
                    <tk:select.option
                        :label="$optionItemLabel"
                        :selected="in_array($optionItemValue, $value)"
                        :value="$optionItemValue"
                    />
                @endif
            @endforeach
        @endif
    </select>
</tk:field>
