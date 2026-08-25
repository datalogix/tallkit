@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'multiple' => null,
    'searchable' => true,
    'animation' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext($attributes, $label, $id);
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
                    'tk-control-wrapper-expanded',
                    TALLKit::roundedSize(size: $size, mode: 'large'),
                    TALLKit::controlFocusRingNested($color, expanded: true),
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
                :aria-expanded="opened ? 'true' : 'false'"
                aria-controls="{{ $id.'-listbox' }}"
                {{
                    $attributes
                        ->dataKey('combobox')
                        ->dataKey('control')
                        ->dataKey('group-target')
                        ->merge([
                            'id' => $id,
                            'aria-describedby' => TALLKit::ariaDescribedBy($id, $description, $help, $invalid, $showError),
                            'aria-invalid' => $invalid ? 'true' : null,
                            'data-invalid' => $invalid ? true : null,
                        ])
                        ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes([
                            'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                            'selected:', 'selected-label:', 'selected-clear:', 'selected-option:',
                            'popover:', 'listbox:', 'heading:', 'option:',
                        ]))
                        ->except('class')
                        ->classes(
                            '
                                tk-field-control-base
                                peer
                                cursor-default

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
                                'tk-control-standalone-expanded',
                                TALLKit::roundedSize(size: $size, mode: 'large'),
                                TALLKit::controlFocusRing($color, expanded: true),
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
                            @click.stop="clearValue()"
                        />
                    </div>
                @endif
            </div>

            <tk:popover
                :attributes="TALLKit::attributesAfter($attributes, 'popover:')
                    ->classes(TALLKit::spaceBlock(size: $size), 'max-h-full')"
                :$size
                :$animation
            >
                <tk:listbox
                    :attributes="TALLKit::attributesAfter($attributes, 'listbox:')"
                    :$searchable
                    :$size
                    :$multiple
                    :standalone="false"
                    :search:color="$color"
                    items:class="focus-visible:ring-0!"
                    items:id="{{ $id.'-listbox' }}"
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
                                    :attributes="TALLKit::attributesAfter($attributes, 'option:')
                                        ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId('combobox-option', (string) $optionItemGroupValue)] : [], false)
                                    "
                                    :value="$optionItemGroupValue"
                                    :label="$optionItemGroupLabel"
                                />
                            @endforeach
                        @else
                            <tk:combobox.option
                                :attributes="TALLKit::attributesAfter($attributes, 'option:')
                                    ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId('combobox-option', (string) $optionItemValue)] : [], false)
                                "
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
