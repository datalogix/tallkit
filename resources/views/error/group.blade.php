@props([
    'bag' => null,
    'title' => null,
    'prepend' => null,
    'append' => null,
    'actions' => null,
])
@php

$errorBag = TALLKit::errorBag(bag: $bag);

@endphp
@if ($errorBag->isNotEmpty())
    <tk:alert
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['title' => null, 'prepend' => null, 'append' => null, 'actions' => null, 'pauseOnHover' => null])"
        :message="$errorBag->all()"
        :icon="false"
        type="danger"
    />
@endif
