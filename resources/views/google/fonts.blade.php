@props([
    'families' => null,
    'display' => null,
    'prefetch' => true,
    'preconnect' => true,
    'preload' => false,
    'useStylesheet' => false,
    'noscript' => false,
])
@php

if (filter_var($families, FILTER_VALIDATE_URL) !== false) {
    $url = $families;
} else {
    $params = collect($families)->map(fn ($family) => 'family='.$family);
    $params->when(! $display && $preload ? 'swap' : $display, fn ($collection, $value) => $collection->push('display='.$value));

    $url = 'https://fonts.googleapis.com/css2?'.$params->join('&');
}

@endphp
@if ($noscript && ! $useStylesheet)
    <noscript><link rel="stylesheet" href="{!! $url !!}" /></noscript>
@endif

@if (! $noscript)
    @if ($prefetch)
        <link rel="dns-prefetch" href="https://fonts.gstatic.com/" />
    @endif

    @if ($preconnect)
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link rel="preconnect" href="https://fonts.googleapis.com/" />
    @endif

    @if ($preload)
        <link rel="preload" as="style" href="{!! $url !!}" />
    @endif

    @if ($useStylesheet)
        <link rel="stylesheet" href="{!! $url !!}" />
    @else
        <script>
            var l=document.createElement('link');
            l.rel='stylesheet';
            l.href='{!! $url !!}';
            document.querySelector("head").appendChild(l);
        </script>
    @endif
@endif
