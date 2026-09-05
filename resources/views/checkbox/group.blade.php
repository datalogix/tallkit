@props([
    'size' => null,
    'variant' => null,
    'align' => null,
    'label' => null,
    'iconOn' => null,
    'iconOff' => null,
    'group' => null,
])
@php

[$name, $fieldName, $label] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label);
$options = TALLKit::parseOptions(attributes: $attributes);

@endphp
@if ($slot->isNotEmpty() || filled($options))
    <tk:fieldset
        :$label
        :$size
        :attributes="$attributes->whereDoesntStartWith(['heading:', 'checkbox:', 'error:'])
            ->classes('[&_[data-tallkit-heading]]:mb-2 [&>[data-tallkit-heading]:not(:first-of-type)]:pt-2')
        "
    >
        {{ $slot }}

        @foreach ($options as $optionItemValue => $optionItemLabel)
            @if (is_array($optionItemLabel))
                <tk:heading
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'heading:')"
                    :label="$optionItemValue"
                    :size="TALLKit::adjustSize(size: $size)"
                />

                @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                    <tk:checkbox
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'checkbox:')
                            ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId(prefix: 'field', name: $fieldName, suffix: (string) $optionItemGroupValue)] : [], false)
                        "
                        :label="$optionItemGroupLabel"
                        :value="$optionItemGroupValue"
                        :show-error="false"
                        :id="TALLKit::generateId(prefix: 'field', name: $fieldName, suffix: (string) $optionItemGroupValue)"
                        :$name
                        :$size
                        :$variant
                        :$align
                        :$iconOn
                        :$iconOff
                        :$group
                    />
                @endforeach
            @else
                <tk:checkbox
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'checkbox:')
                        ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId(prefix: 'field', name: $fieldName, suffix: (string) $optionItemValue)] : [], false)
                    "
                    :label="$optionItemLabel"
                    :value="$optionItemValue"
                    :show-error="false"
                    :id="TALLKit::generateId(prefix: 'field', name: $fieldName, suffix: (string) $optionItemValue)"
                    :$name
                    :$size
                    :$variant
                    :$align
                    :$iconOn
                    :$iconOff
                    :$group
                />
            @endif
        @endforeach

        <tk:error
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'error:')"
            :name="$fieldName"
            :$size
        />
    </tk:fieldset>
@endif
