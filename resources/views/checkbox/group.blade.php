@props([
    'variant' => null,
    'iconOn' => null,
    'iconOff' => null,
])

@if ($slot->isNotEmpty() || filled($options))
    <tk:fieldset
        :attributes="$attributes
            ->whereDoesntStartWith([
                'name', 'id', 'help', 'showError', 'prefix', 'suffix',
                'heading:', 'checkbox:', 'error:',
                'wire:model', // ignore wire:model on fieldset
            ])
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
                    <tk:checkbox
                        :attributes="TALLKit::attributesAfter($attributes, 'checkbox:')"
                        :label="$optionItemGroupLabel"
                        :value="$optionItemGroupValue"
                        :show-error="false"
                        :$name
                        :$size
                        :$variant
                        :$iconOn
                        :$iconOff
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
                    :$iconOn
                    :$iconOff
                />
            @endif
        @endforeach

        <tk:error
            :attributes="TALLKit::attributesAfter($attributes, 'error:')"
            :name="$getFieldName()"
            :$size
        />
    </tk:fieldset>
@endif
