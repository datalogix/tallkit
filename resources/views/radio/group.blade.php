@props([
    'size' => null,
    'align' => null,
    'label' => null,
    'description' => null,
    'value' => null,
])
@php

[$name, $fieldName, $label, , , $wireModel, $groupId] = $attributes->fieldContext(label: $label);

$optionId = fn ($value) => $groupId.'-'.TALLKit::idPart($value);
$modelAttributes = $attributes->whereStartsWith(['wire:model', 'x-model'])->getAttributes() ?: array_filter(['wire:model' => $wireModel]);
$options = TALLKit::parseOptions(attributes: $attributes);
$required = $attributes->enabled('required') ?: null;
$errorId = $fieldName && filled(TALLKit::errorMessage(name: $fieldName, bag: TALLKit::fieldErrorBag($groupId))) ? $groupId.'-error' : null;

@endphp
@if ($slot->isNotEmpty() || filled($options))
    <tk:fieldset
        :$label
        :$size
        :attributes="$attributes->withProps(get_defined_vars(), ['description' => null])->whereDoesntStartWith(['heading:', 'radio:', 'error:', 'wire:model', 'x-model'])->except('required')
            ->classes('[&_[data-tallkit-heading]]:mb-2 [&>[data-tallkit-heading]:not(:first-of-type)]:pt-2')
        "
        :aria-describedby="$errorId"
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
                        "
                        :label="$optionItemGroupLabel"
                        :value="$optionItemGroupValue"
                        :checked="(string) $optionItemGroupValue === (string) $value"
                        :show-error="false"
                        :$required
                        :id="$optionId($optionItemGroupValue)"
                        :name="$name ?? $groupId"
                        :$size
                        :$align
                    />
                @endforeach
            @else
                <tk:radio
                    :attributes="$attributes->prefixed('radio:')
                        ->wireKey($optionId($optionItemValue))
                        ->merge($modelAttributes, false)
                    "
                    :label="$optionItemLabel"
                    :value="$optionItemValue"
                    :checked="(string) $optionItemValue === (string) $value"
                    :show-error="false"
                    :$required
                    :id="$optionId($optionItemValue)"
                    :name="$name ?? $groupId"
                    :$size
                    :$align
                />
            @endif
        @endforeach

        <tk:error
            :attributes="$attributes->prefixed('error:')"
            :id="$groupId.'-error'"
            :bag="TALLKit::fieldErrorBag($groupId)"
            :name="$fieldName"
            :$size
        />
    </tk:fieldset>
@endif
