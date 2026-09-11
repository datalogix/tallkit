@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'multiple' => null,
    'searchable' => true,
    'type' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);

$hasControl = $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');
$options = TALLKit::parseOptions(attributes: $attributes);
$placeholderText = __(is_string($placeholder) ? $placeholder : '---');
$isInputTrigger = $type === 'input';
$disabled = (bool) $attributes->get('disabled');

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
                    TALLKit::controlFocusRingNested(color: $color, expanded: true),
                ),
            )
        "
    >
        <div
            wire:ignore
            x-data="combobox({
                value: @js($value ?? ($multiple ? [] : null)),
                multiple: @js($multiple),
                type: @js($type),
            })"
            class="flex-1"
        >
            @if ($isInputTrigger)
                <div
                    {{
                        TALLKit::attributesAfter(attributes: $attributes, prefix: 'trigger:')
                            ->dataKey('control')
                            ->dataKey('group-target')
                            ->merge([
                                'disabled' => $disabled ?: null,
                                'aria-disabled' => $disabled ? 'true' : null,
                            ])
                            ->except('class')
                            ->classes([
                                '
                                    tk-field-control-base
                                    flex
                                    items-center
                                    flex-wrap
                                    gap-1
                                ',
                                TALLKit::fontSize(size: $size, mode: 'large'),
                                TALLKit::paddingStart(size: $size, mode: 'large'),
                                TALLKit::paddingEnd(size: $size, mode: 'large'),
                                TALLKit::paddingBlock(size: $size, mode: 'smallest'),
                                TALLKit::generateClassBySize(size: $size, name: 'min-h', values: ['8', '9', '10', '12', '14', '16', '18']),
                                $attributes->pluck('combobox:class'),
                                'pointer-events-none' => $disabled,
                            ])
                            ->when(
                                ! $hasControl,
                                fn ($attrs) => $attrs->classes(
                                    'tk-control-standalone-expanded',
                                    TALLKit::roundedSize(size: $size, mode: 'large'),
                                    TALLKit::controlFocusRing(color: $color, expanded: true),
                                ),
                            )
                    }}
                >
                    <input
                        type="hidden"
                        {{
                            $attributes
                                ->dataKey('combobox-field')
                                ->merge([
                                    'name' => $name,
                                    'value' => in_livewire() ? null : (is_array($value) ? implode(',', $value) : $value),
                                    'wire:model' => $wireModel,
                                ])
                                ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                                    'trigger:', 'search:', 'selected:', 'selected-label:', 'selected-clear:', 'selected-option:',
                                    'popover:', 'listbox:', 'heading:', 'option:',
                                ]))
                        }}
                    />

                    @if ($multiple)
                        @foreach ($flatOptions as $optionItemValue => $optionItemLabel)
                            <tk:badge
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'selected-option:')->classes('truncate')"
                                :$size
                                x-cloak
                                x-show="isSelected({{ Js::from((string) $optionItemValue) }})"
                                ::style="{ order: selectedOrder({{ Js::from((string) $optionItemValue) }}) }"
                                @before-dismiss.prevent="remove({{ Js::from((string) $optionItemValue) }})"
                                close
                                close:tooltip="Remove"
                                content:class="block truncate"
                                :label="$optionItemLabel"
                            />
                        @endforeach
                    @endif

                    <input
                        type="text"
                        role="combobox"
                        aria-autocomplete="list"
                        aria-haspopup="listbox"
                        aria-controls="{{ $id.'-listbox' }}"
                        :aria-expanded="opened ? 'true' : 'false'"
                        autocomplete="off"
                        @if ($multiple)
                            :placeholder="selectedCount() > 0 ? '' : {{ Js::from($placeholderText) }}"
                        @else
                            placeholder="{{ $placeholderText }}"
                        @endif
                        {{
                            TALLKit::attributesAfter(attributes: $attributes, prefix: 'search:')
                                ->dataKey('input')
                                ->merge([
                                    'id' => $id,
                                    'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                                    'aria-invalid' => $invalid ? 'true' : null,
                                    'data-invalid' => $invalid ? true : null,
                                    'disabled' => $disabled ?: null,
                                ])
                                ->classes(
                                    '
                                        flex-1 min-w-0 truncate
                                        bg-transparent
                                        border-0 outline-none p-0
                                        disabled:cursor-not-allowed
                                        order-last
                                    '
                                )
                        }}
                    />

                    @unless ($multiple)
                        <tk:button
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'selected-clear:')->classes('shrink-0 order-last')"
                            :size="TALLKit::adjustSize(size: $size)"
                            :$disabled
                            x-show="!this.opened && selectedLabel()"
                            x-cloak
                            tooltip="Clear"
                            variant="none"
                            icon="close"
                            @click.stop="clearValue()"
                        />
                    @endunless

                    <tk:button
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'selected-clear:')->classes('shrink-0 order-last')"
                        :$disabled
                        variant="none"
                        icon="ph:caret-up-down"
                        @click.stop="toggle()"
                    />
                </div>
            @else
                <div
                    tabindex="{{ $disabled ? '-1' : '0' }}"
                    role="combobox"
                    aria-haspopup="listbox"
                    :aria-expanded="opened ? 'true' : 'false'"
                    aria-controls="{{ $id.'-listbox' }}"
                    {{
                        TALLKit::attributesAfter(attributes: $attributes, prefix: 'trigger:')
                            ->dataKey('combobox')
                            ->dataKey('control')
                            ->dataKey('group-target')
                            ->merge([
                                'id' => $id,
                                'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                                'aria-invalid' => $invalid ? 'true' : null,
                                'data-invalid' => $invalid ? true : null,
                                'disabled' => $disabled ?: null,
                                'aria-disabled' => $disabled ? 'true' : null,
                            ])
                            ->except('class')
                            ->classes([
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
                                TALLKit::paddingBlock(size: $size, mode: 'smallest'),
                                TALLKit::generateClassBySize(size: $size, name: 'min-h', values: ['8', '9', '10', '12', '14', '16', '18']),
                                $attributes->pluck('combobox:class'),
                                'pointer-events-none' => $disabled,
                            ])
                            ->when(
                                ! $hasControl,
                                fn ($attrs) => $attrs->classes(
                                    'tk-control-standalone-expanded',
                                    TALLKit::roundedSize(size: $size, mode: 'large'),
                                    TALLKit::controlFocusRing(color: $color, expanded: true),
                                ),
                            )
                    }}
                >
                    <input
                        type="hidden"
                        {{
                            $attributes
                                ->dataKey('combobox-field')
                                ->merge([
                                    'name' => $name,
                                    'value' => in_livewire() ? null : (is_array($value) ? implode(',', $value) : $value),
                                    'wire:model' => $wireModel,
                                ])
                                ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                                    'trigger:', 'search:', 'selected:', 'selected-label:', 'selected-clear:', 'selected-option:',
                                    'popover:', 'listbox:', 'heading:', 'option:',
                                ]))
                        }}
                    />

                    @if ($multiple)
                        <span
                            {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'selected-label:')->classes('truncate', TALLKit::textNeutral(variant: 'muted')) }}
                            x-show="selectedCount() === 0"
                            x-text="@js($placeholderText)"
                        ></span>

                        <div
                            {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'selected:')->classes('truncate flex flex-wrap gap-1') }}
                            x-show="selectedCount() > 0"
                        >
                            @foreach ($flatOptions as $optionItemValue => $optionItemLabel)
                                <tk:badge
                                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'selected-option:')->classes('truncate')"
                                    :$size
                                    x-cloak
                                    x-show="isSelected({{ Js::from((string) $optionItemValue) }})"
                                    ::style="{ order: selectedOrder({{ Js::from((string) $optionItemValue) }}) }"
                                    @before-dismiss.prevent="remove({{ Js::from((string) $optionItemValue) }})"
                                    close
                                    close:tooltip="Remove"
                                    content:class="block truncate"
                                    :label="$optionItemLabel"
                                />
                            @endforeach
                        </div>
                    @else
                        <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'selected:')->classes('truncate flex items-center gap-2') }}>
                            <span
                                {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'selected-label:')->classes('truncate flex-1') }}
                                x-text="selectedLabel() ?? @js($placeholderText)"
                                :class="{ '{{ TALLKit::textNeutral(variant: 'muted') }}': !selectedLabel() }"
                            ></span>

                            <tk:button
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'selected-clear:')->classes('shrink-0')"
                                :size="TALLKit::adjustSize(size: $size)"
                                :$disabled
                                x-show="!this.opened && selectedLabel()"
                                x-cloak
                                tooltip="Clear"
                                variant="none"
                                icon="close"
                                @click.stop="clearValue()"
                            />
                        </div>
                    @endif
                </div>
            @endif

            <tk:popover
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'popover:')
                    ->classes(TALLKit::spaceBlock(size: $size), 'max-h-full')"
                :$size
                animation="none"
            >
                <tk:listbox
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'listbox:')"
                    :$searchable
                    :$size
                    :$multiple
                    :standalone="false"
                    :search:color="$color"
                    items:class="focus-visible:ring-0!"
                    items:id="{{ $id.'-listbox' }}"
                >
                    {{ $slot }}

                    @if ($isInputTrigger)
                        {{-- the trigger input itself is the search field; suppress tk:listbox's own search partial while keeping its no-records state --}}
                        <x-slot:search></x-slot:search>
                    @elseif (isset($search))
                        <x-slot:search>
                            {{ $search }}
                        </x-slot:search>
                    @endif

                    @foreach ($options as $optionItemValue => $optionItemLabel)
                        @if (is_array($optionItemLabel))
                            <tk:heading
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'heading:')->classes('px-1')"
                                :label="$optionItemValue ?: '---'"
                                :size="TALLKit::adjustSize(size: $size)"
                            />

                            @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                                <tk:combobox.option
                                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'option:')
                                        ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId(prefix: 'combobox-option', name: (string) $optionItemGroupValue)] : [], false)
                                    "
                                    :value="$optionItemGroupValue"
                                    :label="$optionItemGroupLabel"
                                />
                            @endforeach
                        @else
                            <tk:combobox.option
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'option:')
                                    ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId(prefix: 'combobox-option', name: (string) $optionItemValue)] : [], false)
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
