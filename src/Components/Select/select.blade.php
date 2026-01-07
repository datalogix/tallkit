<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
>
    <div {{ $attributes->only('class')->classes('w-full relative block group/select')
        ->when(in_livewire() && $loading, fn($attrs) => $attrs->merge([
            'wire:loading.class' => 'field-loading',
            'wire:target' => $wireTarget
        ]))
    }}>
        <select
            {{ $buildDataAttribute('control') }}
            {{ $buildDataAttribute('group-target') }}
            @isset ($name) name="{{ $name }}" @endisset
            @isset ($id) id="{{ $id }}" @endisset
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @if ($multiple) multiple size="{{ $rows ?? 5 }}" @endif
            {{ $attributes->whereDoesntStartWith([
                    'field:', 'label:', 'info:', 'badge:', 'description:', 'help:', 'error:',
                    'group:', 'prefix:', 'suffix:', 'append:', 'loading:',
                    'placeholder:', 'optgroup:', 'option:',
                ])
                ->when(
                    $multiple,
                    fn ($attrs) => $attrs->classes(
                        'overflow-auto bg-none!',
                        match ($size) {
                            'xs' => 'p-1.5 [&_*]:py-1 space-y-1 [&_option]:px-1.5 [&_option]:rounded-md [&>optgroup>option]:ms-2 [&>optgroup]:space-y-1 [&>optgroup>option:first-child]:mt-1 [&>optgroup>option:last-child]:mb-1',
                            'sm' => 'p-1.5 [&_*]:py-1 space-y-1 [&_option]:px-1.5 [&_option]:rounded-md [&>optgroup>option]:ms-2.5 [&>optgroup]:space-y-1 [&>optgroup>option:first-child]:mt-1 [&>optgroup>option:last-child]:mb-1',
                            default => 'p-2 [&_*]:py-1.5 space-y-1.5 [&_option]:px-2 [&_option]:rounded-lg [&>optgroup>option]:ms-3 [&>optgroup]:space-y-1.5 [&>optgroup>option:first-child]:mt-1.5 [&>optgroup>option:last-child]:mb-1.5',
                            'lg' => 'p-2 [&_*]:py-1.5 space-y-1.5 [&_option]:px-2 [&_option]:rounded-lg [&>optgroup>option]:ms-3.5 [&>optgroup]:space-y-1.5 [&>optgroup>option:first-child]:mt-1.5 [&>optgroup>option:last-child]:mb-1.5',
                            'xl' => 'p-2.5 [&_*]:py-2 space-y-2 [&_option]:px-2.5 [&_option]:rounded-lg [&>optgroup>option]:ms-4 [&>optgroup]:space-y-2 [&>optgroup>option:first-child]:mt-2 [&>optgroup>option:last-child]:mb-2',
                            '2xl' => 'p-2.5 [&_*]:py-2 space-y-2 [&_option]:px-2.5 [&_option]:rounded-xl [&>optgroup>option]:ms-4.5 [&>optgroup]:space-y-2 [&>optgroup>option:first-child]:mt-2 [&>optgroup>option:last-child]:mb-2',
                            '3xl' => 'p-3 [&_*]:py-2.5 space-y-2.5 [&_option]:px-3 [&_option]:rounded-xl [&>optgroup>option]:ms-5 [&>optgroup]:space-y-2.5 [&>optgroup>option:first-child]:mt-2.5 [&>optgroup>option:last-child]:mb-2.5',
                        }
                    ),
                    fn ($attrs) => $attrs->classes('ps-3 pe-10')
                )
                ->classes(
                    match ($size) {
                        'xs' => 'min-h-8 text-xs rounded-md [.field-loading_&]:pe-[32px]',
                        'sm' => 'min-h-9 text-sm rounded-md [.field-loading_&]:pe-[36px]',
                        default => 'min-h-10 text-base rounded-lg [.field-loading_&]:pe-[40px]',
                        'lg' => 'min-h-12 text-lg rounded-lg [.field-loading_&]:pe-[48px]',
                        'xl' => 'min-h-14 text-xl rounded-lg [.field-loading_&]:pe-[56px]',
                        '2xl' => 'min-h-16 text-2xl rounded-xl [.field-loading_&]:pe-[64px]',
                        '3xl' => 'min-h-18 text-3xl rounded-xl [.field-loading_&]:pe-[72px]',
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
                        border-zinc-200 border-b-zinc-300/80 dark:border-white/10
                        disabled:border-b-zinc-200 dark:disabled:border-white/5
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
                        disabled:opacity-75
                        dark:disabled:opacity-50

                        has-[option.placeholder:checked]:text-zinc-400 dark:has-[option.placeholder:checked]:text-zinc-400
                        dark:[&_option]:bg-zinc-700 dark:[&_*]:text-white

                        bg-size-[1.5em_1.5em]
                        bg-no-repeat
                        [print-color-adjust:exact]

                        bg-position-[right_.5rem_center]
                        rtl:bg-position-[left_.5rem_center]

                        [.field-loading_&]:bg-none!
                    ',
                )
            }}
        >
            @if ($placeholder ?? true && ! $multiple)
                <tk:select.option
                    :attributes="$attributesAfter('placeholder:')->classes('placeholder')"
                    :label="is_string($placeholder) ? $placeholder : '---'"
                    :disabled="$attributes->has('required')"
                    :selected="true"
                    :value="''"
                />
            @endif

            @if ($slot->hasActualContent())
                {{ $slot }}
            @else
                @foreach ($options as $optionItemValue => $optionItemLabel)
                    @if (is_array($optionItemLabel))
                        <optgroup :attributes="$attributesAfter('optgroup:')" label="{{ __($optionItemValue ?: '---') }}">
                            @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                                <tk:select.option
                                    :attributes="$attributesAfter('option:')"
                                    :label="$optionItemGroupLabel"
                                    :selected="in_array($optionItemGroupValue, $value)"
                                    :value="$optionItemGroupValue"
                                />
                            @endforeach
                        </optgroup>
                    @else
                        <tk:select.option
                            :attributes="$attributesAfter('option:')"
                            :label="$optionItemLabel"
                            :selected="in_array($optionItemValue, $value)"
                            :value="$optionItemValue"
                        />
                    @endif
                @endforeach
            @endif
        </select>

        @if ($loading)
            <div
                {{
                    $attributesAfter('append:')->classes(
                        'absolute top-0 bottom-0 flex gap-x-1.5 pe-3 end-0 text-zinc-400',
                        $multiple ? 'items-start pt-3' : 'items-center',
                    )
                }}
            >
                <tk:loading
                    :attributes="$attributesAfter('loading:')->classes('hidden [.field-loading_&]:block')"
                    :size="$adjustSize()"
                />
            </div>
        @endif
    </div>
</tk:field>
