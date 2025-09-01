<tk:fieldset
    x-data="checkboxGroup"
    :attributes="$attributes
        ->whereDoesntStartWith([
            'name', 'id', 'help', 'showError', 'prefix', 'suffix',
            'heading:', 'checkbox:', 'error:',
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
                <tk:checkbox
                    :attributes="$attributesAfter('checkbox:')"
                    :label="$optionItemGroupLabel"
                    :value="$optionItemGroupValue"
                    :show-error="false"
                    :$name
                    :$size
                />
            @endforeach
        @else
            <tk:checkbox
                :attributes="$attributesAfter('checkbox:')"
                :label="$optionItemLabel"
                :value="$optionItemValue"
                :show-error="false"
                :$name
                :$size
            />
        @endif
    @endforeach

    <tk:error
        :attributes="$attributesAfter('error:')"
        :name="$getFieldName()"
        :$size
    />
</tk:fieldset>
