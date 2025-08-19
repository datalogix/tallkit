<!DOCTYPE html>
<html {{ $attributesAfter('html:')->merge(['lang' => Str::replace($lang, '_', '-')]) }}>
<head {{ $attributesAfter('head:') }}>
    @if ($charset) <meta charset="{{ $charset }}"> @endif
    @if ($viewport) <meta name="viewport" content="{{ $viewport }}"> @endif
    @if ($csrfToken) <meta name="csrf-token" content="{{ csrf_token() }}"> @endif
    @foreach ($metaTags as $metaName => $metaContent) <meta name="{{ $metaName }}" content="{{ $metaContent }}"> @endforeach
    @if ($meta) <tk:html.meta :attributes="$attributesAfter('meta:')->merge(is_array($meta) ? $meta : [])" /> @endif
    @if ($googleFonts) <tk:google.fonts :attributes="$attributesAfter('google-fonts:')->merge($googleFonts)->merge(['noscript' => false])" /> @endif
    <title>{{ $title }}</title>
    {{ $head ?? '' }}
    @if ($appearance) <tk:appearance :nonce="is_string($appearance) ? $appearance : null" /> @endif
    @if ($gtag) <tk:google.gtag :id="$gtag" /> @endif
    @if ($gtm) <tk:google.gtm :id="$gtm" /> @endif
    @if ($typekit) <link href="https://use.typekit.net/{{ $typekit }}.css" rel="stylesheet"> @endif
    @foreach ($styles as $style) <link href="{{ $style }}" rel="stylesheet"> @endforeach
    @if ($stackStyles) @stack($stackStyles) @endif
    @if (Vite::isRunningHot() || Vite::manifestHash($viteBuildDirectory !== null)) @vite($vite, $viteBuildDirectory) @endif
</head>
<body {{
    $attributes
        ->whereDoesntStartWith(['html:', 'head:', 'meta:', 'google-fonts:', 'components:', 'toast:'])
        ->classes(
            'min-h-dvh',
            '[:where(&)]:bg-white [:where(&)]:dark:bg-zinc-900',
            '[:where(&)]:text-zinc-700 [:where(&)]:dark:text-white/70',
            'antialiased',
        )
}}>

    @if ($googleFonts) <tk:google.fonts :attributes="$attributesAfter('google-fonts:')->merge($googleFonts)->merge(['noscript' => true])" /> @endif
    @if ($gtm) <tk:google.gtm :id="$gtm" noscript /> @endif
    {{ $slot }}
    @foreach ($components as $c => $component) <x-dynamic-component :attributes="$attributesAfter('components:')->merge($attrs)" :$component /> @endforeach
    @if ($toast && $livewire) @persist('toast') <tk:toast :attributes="$attributesAfter('toast:')" /> @endpersist @endif
    @if ($toast && !$livewire) <tk:toast :attributes="$attributesAfter('toast:')" /> @endif
    @foreach ($scripts as $script) <script src="{{ $script }}"></script> @endforeach
    @if ($stackScripts) @stack($stackScripts) @endif
</body>
</html>
