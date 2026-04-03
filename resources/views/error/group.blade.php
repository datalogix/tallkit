@props([
    'bag' => null,
])
@php

$errorBag = TALLKit::getErrorBag(bag: $bag);

@endphp
@if ($errorBag->isNotEmpty())
    <tk:alert
        :$attributes
        :message="$errorBag->all()"
        :icon="false"
        type="danger"
    />
@endif
