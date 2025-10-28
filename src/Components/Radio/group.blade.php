<tk:fieldset
    :attributes="$attributes
        ->whereDoesntStartWith([
            'name', 'id', 'help', 'showError', 'prefix', 'suffix',
            'heading:', 'radio:', 'error:',
            'wire:model', // ignore wire:model on fieldset
        ])
        ->classes('[&_[data-tallkit-heading]]:mb-2 [&>[data-tallkit-heading]:not(:first-of-type)]:pt-2')
    "
>
    {{ $slot }}

    @foreach ($options as $optionItemValue => $optionItemLabel)
        @if (is_array($optionItemLabel))
            <tk:heading
                :attributes="$attributesAfter('heading:')"
                :size="$adjustSize()"
                :label="$optionItemValue"
                variant="subtle"
            />

            @foreach ($optionItemLabel as $optionItemGroupValue => $optionItemGroupLabel)
                <tk:radio
                    :attributes="$attributesAfter('radio:')"
                    :label="$optionItemGroupLabel"
                    :value="$optionItemGroupValue"
                    :show-error="false"
                    :$name
                    :$size
                    :$variant
                />
            @endforeach
        @else
            <tk:radio
                :attributes="$attributesAfter('radio:')"
                :label="$optionItemLabel"
                :value="$optionItemValue"
                :show-error="false"
                :$name
                :$size
                :$variant
            />
        @endif
    @endforeach

    <tk:error
        :attributes="$attributesAfter('error:')"
        :name="$getFieldName()"
        :$size
    />
</tk:fieldset>
