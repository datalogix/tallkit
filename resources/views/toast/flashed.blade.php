@props([
    'nonce' => null,
])
@php

$toasts = TALLKit::toastPullFlashed();

@endphp
{{-- Outside the container: a container kept by @persist ignores its new markup. --}}
@if ($toasts !== [])
    <script @if ($nonce) nonce="{{ $nonce }}" @endif>
        (function (toasts) {
            toasts.forEach(function (detail) {
                if (window.tallkit) return window.tallkit.toast(detail)

                ;(window.__tallkitToastQueue = window.__tallkitToastQueue || []).push({ event: @js(TALLKit::eventName('toast')), detail: detail })
            })
        })(@js($toasts))
    </script>
@endif
