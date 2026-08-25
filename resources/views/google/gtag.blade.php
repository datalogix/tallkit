@props([
    'id' => true,
    'nonce' => null,
])
@php

$id = $id === true ? config('services.google.gtag') : $id;

@endphp
@if ($id)
    <!-- Google tag (gtag.js) -->
    <script @if ($nonce) nonce="{{ $nonce }}" @endif async src="https://www.googletagmanager.com/gtag/js?id={{ $id }}"></script>
    <script @if ($nonce) nonce="{{ $nonce }}" @endif>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', @js($id));
    </script>
    <!-- End Google tag (gtag.js) -->
@endif
