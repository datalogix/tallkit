@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'multiple' => null,
    'searchable' => true,
    'trigger' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::fieldContext(attributes: $attributes, label: $label, id: $id, scope: get_defined_vars());
$value = TALLKit::fieldOldValue($fieldName, $value);

if ($multiple && is_string($value)) {
    $value = array_values(array_filter(explode(',', $value), 'strlen'));
}

$listName = $multiple && $name ? Str::finish(Str::before($name, '[]'), '[]') : null;

$hasControl = $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');
$options = TALLKit::parseOptions(attributes: $attributes);
$placeholderText = __(is_string($placeholder) ? $placeholder : '---');
$isInputTrigger = $trigger === 'input';
$disabled = TALLKit::isAttributeEnabled($attributes->get('disabled'));
$readonly = TALLKit::isAttributeEnabled($attributes->get('readonly'));

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <tk:field.control
        :$size
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
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
                trigger: @js($trigger),
            })"
            class="flex-1"
        >
            @if ($isInputTrigger)
                <div
                    {{
                        $attributes->prefixed('trigger:')
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
                                    'name' => $multiple ? null : $name,
                                    'value' => in_livewire() ? null : (is_array($value) ? implode(',', $value) : $value),
                                    'wire:model' => $wireModel,
                                ])
                                ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                                    'trigger:', 'search:', 'selected:', 'selected-label:', 'selected-clear:', 'selected-option:', 'toggle:',
                                    'popover:', 'listbox:', 'heading:', 'option:',
                                ]))
                        }}
                    />

                    @if ($listName && ! in_livewire())
                        <template x-for="item in value" :key="item">
                            <input type="hidden" name="{{ $listName }}" :value="item">
                        </template>
                    @endif

                    @if ($multiple)
                        <template x-for="chosen in selectedValues()" :key="String(chosen)">
                            <tk:badge
                                :attributes="$attributes->prefixed('selected-option:')->classes('truncate')"
                                :$size
                                @before-dismiss.prevent="remove(chosen)"
                                closable
                                close:tooltip="Remove"
                                close:x-bind:aria-label="{{ Js::from(__('Remove')) }} + ': ' + optionLabel(chosen)"
                                content:class="block truncate"
                            >
                                <span x-text="optionLabel(chosen)"></span>
                            </tk:badge>
                        </template>
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
                            $attributes->prefixed('search:')
                                ->dataKey('input')
                                ->merge([
                                    'id' => $id,
                                    'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                                    'aria-invalid' => $invalid ? 'true' : null,
                                    'aria-readonly' => $readonly ? 'true' : null,
                                    'data-invalid' => $invalid ? true : null,
                                    'disabled' => $disabled ?: null,
                                    'readonly' => $readonly ?: null,
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
                            :attributes="$attributes->prefixed('selected-clear:')->classes('shrink-0 order-last')->merge(['tooltip' => 'Clear'])"
                            :size="TALLKit::adjustSize(size: $size)"
                            :$disabled
                            x-show="!opened && selectedLabel()"
                            x-cloak
                            variant="none"
                            icon="close"
                            @click.stop="clearValue()"
                        />
                    @endunless

                    <tk:button
                        :attributes="$attributes->prefixed('toggle:')->classes('shrink-0 order-last')"
                        :$disabled
                        variant="none"
                        icon="ph:caret-up-down"
                        tabindex="-1"
                        aria-hidden="true"
                        @click.stop="toggle()"
                    />
                </div>
            @else
                <div
                    tabindex="{{ $disabled ? '-1' : '0' }}"
                    role="combobox"
                    @if ($label) aria-labelledby="{{ $id }}-label" @endif
                    aria-haspopup="listbox"
                    :aria-expanded="opened ? 'true' : 'false'"
                    aria-controls="{{ $id.'-listbox' }}"
                    {{
                        $attributes->prefixed('trigger:')
                            ->dataKey('combobox')
                            ->dataKey('control')
                            ->dataKey('group-target')
                            ->merge([
                                'id' => $id,
                                'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                                'aria-invalid' => $invalid ? 'true' : null,
                                'aria-readonly' => $readonly ? 'true' : null,
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
                                '[:where(&)]:pe-9',
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
                                    'name' => $multiple ? null : $name,
                                    'value' => in_livewire() ? null : (is_array($value) ? implode(',', $value) : $value),
                                    'wire:model' => $wireModel,
                                ])
                                ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                                    'trigger:', 'search:', 'selected:', 'selected-label:', 'selected-clear:', 'selected-option:', 'toggle:',
                                    'popover:', 'listbox:', 'heading:', 'option:',
                                ]))
                        }}
                    />

                    @if ($listName && ! in_livewire())
                        <template x-for="item in value" :key="item">
                            <input type="hidden" name="{{ $listName }}" :value="item">
                        </template>
                    @endif

                    @if ($multiple)
                        <span
                            {{ $attributes->prefixed('selected-label:')->classes('truncate', TALLKit::textNeutral(variant: 'muted')) }}
                            x-show="selectedCount() === 0"
                            x-text="@js($placeholderText)"
                        ></span>

                        <div
                            {{ $attributes->prefixed('selected:')->classes('truncate flex flex-wrap gap-1') }}
                            x-show="selectedCount() > 0"
                        >
                            <template x-for="chosen in selectedValues()" :key="String(chosen)">
                                <tk:badge
                                    :attributes="$attributes->prefixed('selected-option:')->classes('truncate')"
                                    :$size
                                    @before-dismiss.prevent="remove(chosen)"
                                    closable
                                    close:tooltip="Remove"
                                    close:tabindex="-1"
                                    close:x-bind:aria-label="{{ Js::from(__('Remove')) }} + ': ' + optionLabel(chosen)"
                                    content:class="block truncate"
                                >
                                    <span x-text="optionLabel(chosen)"></span>
                                </tk:badge>
                            </template>
                        </div>
                    @else
                        <div {{ $attributes->prefixed('selected:')->classes('truncate flex items-center gap-2') }}>
                            <span
                                {{ $attributes->prefixed('selected-label:')->classes('truncate flex-1') }}
                                x-text="selectedLabel() ?? @js($placeholderText)"
                                :class="{ '{{ TALLKit::textNeutral(variant: 'muted') }}': !selectedLabel() }"
                            ></span>

                            <tk:button
                                :attributes="$attributes->prefixed('selected-clear:')->classes('shrink-0')->merge(['tooltip' => 'Clear'])"
                                :size="TALLKit::adjustSize(size: $size)"
                                :$disabled
                                tabindex="-1"
                                x-show="!opened && selectedLabel()"
                                x-cloak
                                variant="none"
                                icon="close"
                                @click.stop="clearValue()"
                            />
                        </div>
                    @endif
                </div>
            @endif

            <tk:popover
                :attributes="$attributes->prefixed('popover:')
                    ->classes(TALLKit::spaceBlock(size: $size), 'max-h-full')"
                :$size
                animation="none"
            >
                <tk:listbox
                    :attributes="$attributes->prefixed('listbox:')"
                    :$searchable
                    :$size
                    :$multiple
                    :standalone="false"
                    :search:color="$color"
                    items:class="focus-visible:ring-0!"
                    items:id="{{ $id.'-listbox' }}"
                    :items:aria-labelledby="$label ? $id.'-label' : null"
                >
                    {{ $slot }}

                    @if ($isInputTrigger)
                        <x-slot:search></x-slot:search>
                    @elseif (isset($search))
                        <x-slot:search>
                            {{ $search }}
                        </x-slot:search>
                    @endif

                    @php
                        $optionRows = [];

                        foreach ($options as $optionItemValue => $optionItemLabel) {
                            if (is_array($optionItemLabel)) {
                                $optionRows[] = ['heading', $optionItemValue, null];

                                foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel) {
                                    $optionRows[] = ['option', $optionItemGroupValue, $optionItemGroupLabel];
                                }
                            } else {
                                $optionRows[] = ['option', $optionItemValue, $optionItemLabel];
                            }
                        }

                        $optionTemplate = null;
                    @endphp

                    @if ($optionRows !== [])
                        @php
                            ob_start();
                        @endphp
                        <tk:combobox.option
                            :attributes="$attributes->prefixed('option:')->merge(['wire:key' => '__tallkit_option_key__'])"
                            value="__tallkit_option_value__"
                            label="__tallkit_option_label__"
                        />
                        @php
                            $optionTemplate = ob_get_clean();
                        @endphp
                    @endif

                    @php
                        $stampOption = function ($value, $label) use ($optionTemplate) {
                            if (is_int($label) || is_float($label)) {
                                $label = (string) $label;
                            }

                            if ($optionTemplate === null || ! is_string($label) || $label === '' || ! (is_string($value) || is_int($value))) {
                                return null;
                            }

                            $key = TALLKit::generateId(prefix: 'combobox-option', name: (string) $value);
                            $livewire = in_livewire();
                            $text = e(__($label));

                            return strtr($optionTemplate, [
                                'id="__tallkit_option_key__"' => 'id="'.($livewire ? $key : TALLKit::stableId('listbox-item')).'"',
                                ' wire:key="__tallkit_option_key__"' => $livewire ? ' wire:key="'.$key.'"' : '',
                                '="__tallkit_option_value__"' => '="'.trim(e((string) $value)).'"',
                                '="__tallkit_option_label__"' => '="'.trim($text).'"',
                                '__tallkit_option_label__' => str_contains($text, "\n") ? nl2br($text) : $text,
                            ]);
                        };
                    @endphp

                    @foreach ($optionRows as [$optionRowType, $optionItemValue, $optionItemLabel])
                        @if ($optionRowType === 'heading')
                            <tk:heading
                                :attributes="$attributes->prefixed('heading:')->classes('px-1')"
                                :label="$optionItemValue ?: '---'"
                                :size="TALLKit::adjustSize(size: $size)"
                            />
                        @elseif (($stampedOption = $stampOption($optionItemValue, $optionItemLabel)) !== null)
                            {!! $stampedOption !!}
                        @else
                            <tk:combobox.option
                                :attributes="$attributes->prefixed('option:')
                                    ->wireKey(TALLKit::generateId(prefix: 'combobox-option', name: (string) $optionItemValue))
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
