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

[$name, $fieldName, $label] = TALLKit::resolveFieldContext($attributes, $label);
$options = TALLKit::parseOptions($attributes);

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
                    :attributes="TALLKit::attributesAfter($attributes, 'heading:')"
                    :label="$optionItemValue"
                    :size="TALLKit::adjustSize(size: $size)"
                />

                @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                    <tk:checkbox
                        :attributes="TALLKit::attributesAfter($attributes, 'checkbox:')"
                        :label="$optionItemGroupLabel"
                        :value="$optionItemGroupValue"
                        :show-error="false"
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
                    :attributes="TALLKit::attributesAfter($attributes, 'checkbox:')"
                    :label="$optionItemLabel"
                    :value="$optionItemValue"
                    :show-error="false"
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
            :attributes="TALLKit::attributesAfter($attributes, 'error:')"
            :name="$fieldName"
            :$size
        />
    </tk:fieldset>
@endif
