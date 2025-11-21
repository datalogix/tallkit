@php
$errorBag = $errors->getBag($bag ?? 'default');
@endphp

@if ($errorBag->isNotEmpty())
    <tk:alert
        :$attributes
        :message="$errorBag->all()"
        type="danger"
    />
@endif
