@props([
    'prefix' => null,
    'suffix' => null,
    'size' => null,
])
<tk:field.wrapper
    :attributes="$attributes->whereDoesntStartWith(['prefix:', 'suffix:'])"
    :prefix="null"
    :suffix="null"
    label:as="{{ $attributes->has('label:for') ? 'label' : 'span' }}"
>
    <div {{ $attributes->only('class')->classes('tk-field-group') }}>
        @if ($prefix || TALLKit::attributesAfter($attributes, 'prefix:')->isNotEmpty())
            <tk:field.group.prefix
                :attributes="TALLKit::attributesAfter($attributes, 'prefix:')"
                :$size
            >
                {!! $prefix !!}
            </tk:field.group.prefix>
        @endif

        {{ $slot }}

        @if ($suffix || TALLKit::attributesAfter($attributes, 'suffix:')->isNotEmpty())
            <tk:field.group.suffix
                :attributes="TALLKit::attributesAfter($attributes, 'suffix:')"
                :$size
            >
                {!! $suffix !!}
            </tk:field.group.suffix>
        @endif
    </div>
</tk:field.wrapper>
