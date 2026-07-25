@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'multiple' => null,
    'searchable' => true,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel] = TALLKit::resolveFieldContext($attributes, $label);
$hasControl = $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');
$options = TALLKit::parseOptions($attributes);
$placeholderText = __(is_string($placeholder) ? $placeholder : '---');

$flatOptions = collect();

foreach ($options as $optionItemValue => $optionItemLabel) {
    if (is_array($optionItemLabel)) {
        foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel) {
            $flatOptions[$optionItemGroupValue] = $optionItemGroupLabel;
        }
    } else {
        $flatOptions[$optionItemValue] = $optionItemLabel;
    }
}

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

                        has-[[data-tallkit-control]:is(:focus-visible,[aria-expanded=true])]:outline-2
                        has-[[data-tallkit-control]:is(:focus-visible,[aria-expanded=true])]:outline-blue-700
                        dark:has-[[data-tallkit-control]:is(:focus-visible,[aria-expanded=true])]:outline-blue-300
                        has-[[data-tallkit-control]:is(:focus-visible,[aria-expanded=true])]:outline-offset-0

                        has-[[data-tallkit-control]:is(:focus-visible,[aria-expanded=true])]:ring-2
                        has-[[data-tallkit-control]:is(:focus-visible,[aria-expanded=true])]:ring-blue-700/20
                        dark:has-[[data-tallkit-control]:is(:focus-visible,[aria-expanded=true])]:ring-blue-300/20

                        [&_[data-tallkit-control]]:outline-none
                    ',
                    TALLKit::roundedSize(size: $size, mode: 'large'),
                ),
            )
        "
    >
        <div
            wire:ignore
            x-data="combobox({
                value: @js($value ?? ($multiple ? [] : null)),
                multiple: @js($multiple),
            })"
            x-modelable="value"
            {{ $attributes->only([])->merge(['wire:model' => $wireModel]) }}
        >
            <div
                tabindex="0"
                role="combobox"
                aria-haspopup="listbox"
                aria-expanded="false"
                @if ($multiple) aria-multiselectable="true" @endif
                @if ($invalid) aria-invalid="true" data-invalid @endif
                {{
                    $attributes
                        ->dataKey('combobox')
                        ->dataKey('control')
                        ->dataKey('group-target')
                        ->whereDoesntStartWith([
                            'field:', 'label:', 'info:', 'badge:', 'description:',
                            'group:', 'prefix:', 'suffix:',
                            'help:', 'error:',
                            'control:', 'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                            'selected:', 'selected-label:', 'selected-clear:', 'selected-option:',
                            'popover:', 'listbox:', 'heading:', 'option:',
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
                                cursor-default
                                [print-color-adjust:exact]

                                text-zinc-700
                                disabled:text-zinc-500
                                dark:text-zinc-300
                                dark:disabled:text-zinc-400

                                truncate

                                bg-size-[1.5em_1.5em]
                                bg-no-repeat

                                bg-position-[right_.5rem_center]
                                rtl:bg-position-[left_.5rem_center]

                                flex
                                items-center
                                flex-wrap
                                gap-1
                            ',
                            TALLKit::fontSize(size: $size, mode: 'large'),
                            TALLKit::paddingStart(size: $size, mode: 'large'),
                            TALLKit::paddingEnd(size: $size, mode: 'large'),
                            TALLKit::paddingBlock(size: $size, mode: 'small'),
                            TALLKit::generateClassBySize(size: $size, name: 'min-h', values: ['8', '9', '10', '12', '14', '16', '18']),
                            $attributes->pluck('combobox:class'),
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

                                    [&:is(:focus-visible,[aria-expanded=true])]:outline-2
                                    [&:is(:focus-visible,[aria-expanded=true])]:outline-blue-700
                                    dark:[&:is(:focus-visible,[aria-expanded=true])]:outline-blue-300
                                    [&:is(:focus-visible,[aria-expanded=true])]:outline-offset-0

                                    [&:is(:focus-visible,[aria-expanded=true])]:ring-2
                                    [&:is(:focus-visible,[aria-expanded=true])]:ring-blue-700/20
                                    dark:[&:is(:focus-visible,[aria-expanded=true])]:ring-blue-300/20
                                ',
                                TALLKit::roundedSize(size: $size, mode: 'large'),
                            ),
                        )
                }}
            >
                @if ($multiple)
                    <span
                        {{ TALLKit::attributesAfter($attributes, 'selected-label:')->classes('truncate') }}
                        x-show="opened || selectedCount === 0"
                        x-text="selectedCount > 0 ? `${selectedCount} ${@js(__('selected'))}` : @js($placeholderText)"
                        :class="{ 'text-zinc-400': selectedCount === 0 }"
                    ></span>

                    <div
                        {{ TALLKit::attributesAfter($attributes, 'selected:')->classes('truncate flex flex-wrap gap-1') }}
                        x-show="!opened && selectedCount > 0"
                    >
                        @foreach ($flatOptions as $optionItemValue => $optionItemLabel)
                            <tk:badge
                                :attributes="TALLKit::attributesAfter($attributes, 'selected-option:')->classes('truncate')"
                                :$size
                                x-cloak
                                x-show="isSelected({{ Js::from((string) $optionItemValue) }})"
                                @before-dismiss.prevent="remove({{ Js::from((string) $optionItemValue) }})"
                                close
                                close:tooltip="Remove"
                                content:class="block truncate"
                                :label="$optionItemLabel"
                            />
                        @endforeach
                    </div>
                @else
                    <div {{ TALLKit::attributesAfter($attributes, 'selected:')->classes('truncate flex items-center gap-2') }}>
                        <span
                            {{ TALLKit::attributesAfter($attributes, 'selected-label:')->classes('truncate flex-1') }}
                            x-text="selectedLabel ?? @js($placeholderText)"
                            :class="{ 'text-zinc-400': !selectedLabel }"
                        ></span>

                        <tk:button
                            :attributes="TALLKit::attributesAfter($attributes, 'selected-clear:')->classes('shrink-0')"
                            :size="TALLKit::adjustSize(size: $size)"
                            x-show="!this.opened && selectedLabel"
                            x-cloak
                            tooltip="Clear"
                            variant="none"
                            icon="close"
                            tabindex="-1"
                            @click.stop="clearValue()"
                        />
                    </div>
                @endif
            </div>

            <tk:popover
                :attributes="TALLKit::attributesAfter($attributes, 'popover:')
                    ->classes(TALLKit::spaceBlock(size: $size), 'max-h-full')"
                :$size
            >
                <tk:listbox
                    :attributes="TALLKit::attributesAfter($attributes, 'listbox:')"
                    :$searchable
                    :$size
                    :$multiple
                    :standalone="false"
                    items:class="focus-visible:ring-0!"
                >
                    {{ $slot }}

                    @isset ($search)
                        <x-slot:search>
                            {{ $search }}
                        </x-slot:search>
                    @endisset

                    @foreach ($options as $optionItemValue => $optionItemLabel)
                        @if (is_array($optionItemLabel))
                            <tk:heading
                                :attributes="TALLKit::attributesAfter($attributes, 'heading:')->classes('px-1')"
                                :label="$optionItemValue ?: '---'"
                                :size="TALLKit::adjustSize(size: $size)"
                            />

                            @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                                <tk:combobox.option
                                    :attributes="TALLKit::attributesAfter($attributes, 'option:')"
                                    :value="$optionItemGroupValue"
                                    :label="$optionItemGroupLabel"
                                />
                            @endforeach
                        @else
                            <tk:combobox.option
                                :attributes="TALLKit::attributesAfter($attributes, 'option:')"
                                :value="$optionItemValue"
                                :label="$optionItemLabel"
                            />
                        @endif
                    @endforeach
                </tk:listbox>
            </tk:popover>
        </div>
    </tk:field.control>
</tk:field.wrapper>
