@props([
    'size' => null,
    'align' => null,
    'label' => null,
    'description' => null,
    'value' => null,
])
@php

[$name, $fieldName, $label, , , $wireModel, $groupId] = TALLKit::fieldContext(attributes: $attributes, label: $label);

$optionId = fn ($value) => $groupId.'-'.TALLKit::idPart($value);
$modelAttributes = $attributes->whereStartsWith(['wire:model', 'x-model'])->getAttributes() ?: array_filter(['wire:model' => $wireModel]);
$options = TALLKit::parseOptions(attributes: $attributes);
$required = TALLKit::isAttributeEnabled($attributes->get('required')) ?: null;
$errorId = $fieldName && filled(TALLKit::errorMessage(name: $fieldName, bag: TALLKit::fieldErrorBag($groupId))) ? $groupId.'-error' : null;

@endphp
@if ($slot->isNotEmpty() || filled($options))
    <tk:fieldset
        :$label
        :$size
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['description' => null])->whereDoesntStartWith(['heading:', 'radio:', 'error:', 'wire:model', 'x-model'])->except('required')
            ->mergeDefined(['aria-describedby' => $errorId])
            ->classes('[&_[data-tallkit-heading]]:mb-2 [&>[data-tallkit-heading]:not(:first-of-type)]:pt-2')
        "
    >
        {{ $slot }}

        @foreach ($options as $optionItemValue => $optionItemLabel)
            @if (is_array($optionItemLabel))
                <tk:heading
                    :attributes="$attributes->prefixed('heading:')"
                    :size="TALLKit::adjustSize(size: $size)"
                    :label="$optionItemValue"
                />

                @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                    <tk:radio
                        :attributes="$attributes->prefixed('radio:')
                            ->wireKey($optionId($optionItemGroupValue))
                            ->merge($modelAttributes, false)
                        ->merge(['required' => $required], false)
                        "
                        :label="$optionItemGroupLabel"
                        :value="$optionItemGroupValue"
                        :checked="(string) $optionItemGroupValue === (string) $value"
                        :show-error="false"
                        :id="$optionId($optionItemGroupValue)"
                        :$name
                        :$size
                        :$align
                    />
                @endforeach
            @else
                <tk:radio
                    :attributes="$attributes->prefixed('radio:')
                        ->wireKey($optionId($optionItemValue))
                        ->merge($modelAttributes, false)
                        ->merge(['required' => $required], false)
                    "
                    :label="$optionItemLabel"
                    :value="$optionItemValue"
                    :checked="(string) $optionItemValue === (string) $value"
                    :show-error="false"
                    :id="$optionId($optionItemValue)"
                    :$name
                    :$size
                    :$align
                />
            @endif
        @endforeach

        <tk:error
            :attributes="$attributes->prefixed('error:')->merge(['id' => $groupId.'-error'])"
            :bag="TALLKit::fieldErrorBag($groupId)"
            :name="$fieldName"
            :$size
        />
    </tk:fieldset>
@endif
