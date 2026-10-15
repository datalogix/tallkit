@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'id' => null,
    'accept' => null,
    'maxSize' => null,
    'maxFiles' => null,
    'multiple' => true,
])
@php

$hint = TALLKit::uploadHintText($accept, $maxSize, $maxFiles, (bool) $multiple);

@endphp
@if ($hint)
    <tk:text
        :attributes="$attributes->withElementProps(get_defined_vars())->merge(['variant' => 'subtle'])"
        :id="$id ? $id . '-hint' : null"
        :size="TALLKit::adjustSize(size: $size)"
        :label="$hint"
    />
@endif
