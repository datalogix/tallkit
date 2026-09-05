@props([
    'size' => null,
    'variant' => null,
    'align' => null,
    'label' => null,
    'value' => null,
])
@php

[$name, $fieldName, $label, , , $wireModel] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label);
$wireModel = $attributes->whereStartsWith('wire:model')->first() ?: $wireModel;
$options = TALLKit::parseOptions(attributes: $attributes);

@endphp
@if ($slot->isNotEmpty() || filled($options))
    <tk:fieldset
        :$label
        :$size
        :attributes="$attributes->whereDoesntStartWith(['heading:', 'radio:', 'error:'])
            ->classes('[&_[data-tallkit-heading]]:mb-2 [&>[data-tallkit-heading]:not(:first-of-type)]:pt-2')
        "
    >
        {{ $slot }}

        @foreach ($options as $optionItemValue => $optionItemLabel)
            @if (is_array($optionItemLabel))
                <tk:heading
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'heading:')"
                    :size="TALLKit::adjustSize(size: $size)"
                    :label="$optionItemValue"
                />

                @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                    <tk:radio
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'radio:')
                            ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId(prefix: 'field', name: $fieldName, suffix: (string) $optionItemGroupValue)] : [], false)
                            ->merge(['wire:model' => $wireModel], false)
                        "
                        :label="$optionItemGroupLabel"
                        :value="$optionItemGroupValue"
                        :checked="(string) $optionItemGroupValue === (string) $value"
                        :show-error="false"
                        :id="TALLKit::generateId(prefix: 'field', name: $fieldName, suffix: (string) $optionItemGroupValue)"
                        :$name
                        :$size
                        :$variant
                        :$align
                    />
                @endforeach
            @else
                <tk:radio
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'radio:')
                        ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId(prefix: 'field', name: $fieldName, suffix: (string) $optionItemValue)] : [], false)
                        ->merge(['wire:model' => $wireModel], false)
                    "
                    :label="$optionItemLabel"
                    :value="$optionItemValue"
                    :checked="(string) $optionItemValue === (string) $value"
                    :show-error="false"
                    :id="TALLKit::generateId(prefix: 'field', name: $fieldName, suffix: (string) $optionItemValue)"
                    :$name
                    :$size
                    :$variant
                    :$align
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
