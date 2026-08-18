@props([
    'size' => null,
    'accept' => null,
    'maxSize' => null,
    'maxFiles' => null,
])
@php

$hint = collect([
    $maxFiles ? __('Up to :count files', ['count' => $maxFiles]) : null,
    $maxSize ? __('max :size each', ['size' => \Illuminate\Support\Number::fileSize($maxSize * 1024)]) : null,
    $accept,
])->filter()->implode(' · ');

@endphp
@if ($hint)
    <tk:text
        :$attributes
        :size="TALLKit::adjustSize(size: $size)"
        :label="$hint"
        variant="subtle"
    />
@endif
