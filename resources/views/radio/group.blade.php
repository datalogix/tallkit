@props([
    'size' => null,
    'variant' => null,
    'align' => null,
    'label' => null,
])
@php

[$name, $fieldName, $label] = TALLKit::resolveFieldContext($attributes, $label);
$options = TALLKit::parseOptions($attributes);

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
                    :attributes="TALLKit::attributesAfter($attributes, 'heading:')"
                    :size="TALLKit::adjustSize(size: $size)"
                    :label="$optionItemValue"
                />

                @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                    <tk:radio
                        :attributes="TALLKit::attributesAfter($attributes, 'radio:')
                            ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId('field', $fieldName, (string) $optionItemGroupValue)] : [], false)
                        "
                        :label="$optionItemGroupLabel"
                        :value="$optionItemGroupValue"
                        :show-error="false"
                        :id="TALLKit::generateId('field', $fieldName, (string) $optionItemGroupValue)"
                        :$name
                        :$size
                        :$variant
                        :$align
                    />
                @endforeach
            @else
                <tk:radio
                    :attributes="TALLKit::attributesAfter($attributes, 'radio:')
                        ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId('field', $fieldName, (string) $optionItemValue)] : [], false)
                    "
                    :label="$optionItemLabel"
                    :value="$optionItemValue"
                    :show-error="false"
                    :id="TALLKit::generateId('field', $fieldName, (string) $optionItemValue)"
                    :$name
                    :$size
                    :$variant
                    :$align
                />
            @endif
        @endforeach

        <tk:error
            :attributes="TALLKit::attributesAfter($attributes, 'error:')"
            :name="$fieldName"
            :$size
        />
    </tk:fieldset>
@endif
