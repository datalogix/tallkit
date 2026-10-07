@props([
    'size' => null,
    'align' => null,
    'label' => null,
    'description' => null,
    'iconOn' => null,
    'iconOff' => null,
    'group' => null,
    'value' => null,
])
@php

[$name, $fieldName, $label, , , , $groupId] = TALLKit::fieldContext(attributes: $attributes, label: $label);

$optionId = fn ($value) => $groupId.'-'.TALLKit::idPart($value);
$modelAttributes = $attributes->whereStartsWith(['wire:model', 'x-model'])->getAttributes();
$options = TALLKit::parseOptions(attributes: $attributes);
$optionName = $name && ! str_ends_with($name, '[]') ? $name.'[]' : $name;
$checkedValues = $value === null ? null : array_map('strval', Arr::wrap($value));
$isChecked = fn ($optionValue) => $checkedValues === null ? null : in_array((string) $optionValue, $checkedValues, true);
$errorId = $fieldName && filled(TALLKit::errorMessage(name: $fieldName, bag: TALLKit::fieldErrorBag($groupId))) ? $groupId.'-error' : null;

@endphp
@if ($slot->isNotEmpty() || filled($options))
    <tk:fieldset
        :$label
        :$size
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['description' => null])->whereDoesntStartWith(['heading:', 'checkbox:', 'error:', 'wire:model', 'x-model'])
            ->mergeDefined(['aria-describedby' => $errorId])
            ->classes('[&_[data-tallkit-heading]]:mb-2 [&>[data-tallkit-heading]:not(:first-of-type)]:pt-2')
        "
    >
        {{ $slot }}

        @foreach ($options as $optionItemValue => $optionItemLabel)
            @if (is_array($optionItemLabel))
                <tk:heading
                    :attributes="$attributes->prefixed('heading:')"
                    :label="$optionItemValue"
                    :size="TALLKit::adjustSize(size: $size)"
                />

                @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                    <tk:checkbox
                        :attributes="$attributes->prefixed('checkbox:')
                            ->wireKey($optionId($optionItemGroupValue))
                            ->merge($modelAttributes, false)
                        "
                        :label="$optionItemGroupLabel"
                        :value="$optionItemGroupValue"
                        :checked="$isChecked($optionItemGroupValue)"
                        :show-error="false"
                        :id="$optionId($optionItemGroupValue)"
                        :name="$optionName"
                        :$size
                        :$align
                        :$iconOn
                        :$iconOff
                        :$group
                    />
                @endforeach
            @else
                <tk:checkbox
                    :attributes="$attributes->prefixed('checkbox:')
                        ->wireKey($optionId($optionItemValue))
                        ->merge($modelAttributes, false)
                    "
                    :label="$optionItemLabel"
                    :value="$optionItemValue"
                    :checked="$isChecked($optionItemValue)"
                    :show-error="false"
                    :id="$optionId($optionItemValue)"
                    :name="$optionName"
                    :$size
                    :$align
                    :$iconOn
                    :$iconOff
                    :$group
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
