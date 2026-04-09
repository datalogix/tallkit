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
        :attributes="$attributes->whereDoesntStartWith(['heading:', 'radio:', 'error:'])
            ->classes('[&_[data-tallkit-heading]]:mb-2 [&>[data-tallkit-heading]:not(:first-of-type)]:pt-2')
        "
    >
        {{ $slot }}

        @foreach ($options as $optionItemValue => $optionItemLabel)
            @if (is_array($optionItemLabel))
                <tk:heading
                    :attributes="TALLKit::attributesAfter($attributes, 'heading:')"
                    :label="$optionItemValue"
                    :size="TALLKit::adjustSize(move: -2)"
                />

                @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                    <tk:radio
                        :attributes="TALLKit::attributesAfter($attributes, 'radio:')"
                        :label="$optionItemGroupLabel"
                        :value="$optionItemGroupValue"
                        :show-error="false"
                        :$name
                        :$size
                        :$variant
                        :$align
                    />
                @endforeach
            @else
                <tk:radio
                    :attributes="TALLKit::attributesAfter($attributes, 'radio:')"
                    :label="$optionItemLabel"
                    :value="$optionItemValue"
                    :show-error="false"
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
