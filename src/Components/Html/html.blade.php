<!DOCTYPE html>
<html {{ $attributesAfter('html:') }} lang="{{ str_replace('_', '-', $lang) }}">

<head {{ $attributesAfter('head:') }}>
    @if ($charset)
    <meta charset="{{ $charset }}"> @endif
    @if ($viewport)
    <meta name="viewport" content="{{ $viewport }}"> @endif
    @if ($csrfToken)
    <meta name="csrf-token" content="{{ csrf_token() }}"> @endif
    @foreach ($metaTags as $metaName => $metaContent)
    <meta name="{{ $metaName }}" content="{{ $metaContent }}"> @endforeach
    @if ($meta)
    <tk:html.meta :attributes="$attributesAfter('meta:')->merge($meta)" /> @endif
    @if ($googleFonts)
    <tk:google.fonts :attributes="$attributesAfter('google-fonts:')->merge($googleFonts)->merge(['noscript' => false])" /> @endif
    <title>{{ $title }}</title>
    {{ $head ?? '' }}
    @if ($gtag)
    <tk:google.gtag :id="$gtag" /> @endif
    @if ($gtm)
    <tk:google.gtm :id="$gtm" /> @endif
    @if ($typekit)
    <link href="https://use.typekit.net/{{ $typekit }}.css" rel="stylesheet"> @endif
    @foreach ($styles as $style)
    <link href="{{ $style }}" rel="stylesheet"> @endforeach
    @if ($stackStyles) @stack($stackStyles) @endif
    @if (Vite::isRunningHot() || !is_null(Vite::manifestHash($viteBuildDirectory))) @vite($vite, $viteBuildDirectory)
    @endif
</head>

<body {{ $attributes->whereDoesntStartWith(['html:', 'head:', 'meta:', 'google-fonts:'])
    ->classes(
        'min-h-dvh',
        'bg-white dark:bg-zinc-800',
        'text-zinc-500 dark:text-white/70'
    )
}}">
    @if ($googleFonts)
        <tk:google.fonts :attributes="$attributesAfter('google-fonts:')->merge($googleFonts)->merge(['noscript' => true])" />
    @endif
    @if ($gtm)
    <tk:google.gtm :id="$gtm" noscript /> @endif
    {{ $slot }}
    @foreach ($components as $c => $component) <x-dynamic-component
    :attributes="$attributesAfter('components:')->merge($attrs)" :$component /> @endforeach
    @foreach ($scripts as $script)
    <script src="{{ $script }}"></script> @endforeach
    @if ($stackScripts) @stack($stackScripts) @endif
</body>

</html>
