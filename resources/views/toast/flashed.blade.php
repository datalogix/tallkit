@props([
    'nonce' => null,
])
@php

$toasts = TALLKit::toastPullFlashed();

@endphp
@if ($toasts !== [])
    <script @if ($nonce) nonce="{{ $nonce }}" @endif>
        (function (toasts) {
            toasts.forEach(function (detail) {
                if (window.tallkit) return window.tallkit.toast(detail)

                ;(window.__tallkitToastQueue = window.__tallkitToastQueue || []).push({ event: {{ Js::from(TALLKit::eventName('toast')) }}, detail: detail })
            })
        })({{ Js::from($toasts) }})
    </script>
@endif
