@props([
    'lang' => null,
    'title' => null,
    'charset' => 'utf-8',
    'viewport' => 'width=device-width, initial-scale=1',
    'favicon' => true,
    'themeColor' => null,
    'csrfToken' => true,
    'metaTags' => [],
    'meta' => false,
    'vite' => [
        'resources/css/app.css',
        'resources/css/app.scss',
        'resources/css/app.sass',
        'resources/js/app.js',
        'resources/js/app.ts',
    ],
    'viteBuildDirectory' => 'build',
    'googleFonts' => null,
    'gtag' => true,
    'gtm' => true,
    'typekit' => null,
    'styles' => [],
    'scripts' => [],
    'stackStyles' => 'styles',
    'stackScripts' => 'scripts',
    'components' => [],
    'toast' => true,
    'livewire' => null,
    'appearance' => true,
    'nonce' => null,
    'consent' => true,
])
@php

$lang = TALLKit::resolveLocale($lang);
$dir = in_array(Str::before($lang, '-'), config('app.rtl_locales', ['ar', 'fa', 'he', 'ur'])) ? 'rtl' : 'ltr';
$title ??= config('app.name');
$favicon = $favicon === true ? TALLKit::findAsset(['favicon.ico', 'favicon.svg', 'favicon.png']) : $favicon;
$vite = collect($vite)->unique()->filter(fn ($path) => file_exists(base_path($path)))->toArray();
$googleFonts = is_string($googleFonts) ? ['families' => $googleFonts] : $googleFonts;
$livewire ??= TALLKit::livewireInstalled();
$nonce ??= Vite::cspNonce();

// Unescaped: <x-dynamic-component> escapes bound attributes, and a bag's are escaped already.
$componentAttributes = new \Illuminate\View\ComponentAttributeBag(array_map(
    fn ($value) => is_string($value) ? htmlspecialchars_decode($value, ENT_QUOTES) : $value,
    $attributes->prefixed('components:')->getAttributes(),
));

@endphp
<!DOCTYPE html>
<html
    {{
        $attributes->prefixed('html:')
            ->merge([
                'lang' => $lang,
                'dir' => $dir,
            ])
    }}
>
<head {{ $attributes->prefixed('head:') }}>
    @if ($charset) <meta charset="{{ $charset }}"> @endif
    @if ($viewport) <meta name="viewport" content="{{ $viewport }}"> @endif
    @if ($favicon) <link rel="icon" href="{{ $favicon }}"> @endif
    @if ($themeColor) <meta name="theme-color" content="{{ $themeColor }}"> @endif
    @if ($csrfToken && session()->isStarted()) <meta name="csrf-token" content="{{ csrf_token() }}"> @endif
    @foreach ($metaTags as $metaName => $metaContent) <meta name="{{ $metaName }}" content="{{ $metaContent }}"> @endforeach
    @if ($meta) <tk:html.meta :attributes="$attributes->prefixed('meta:')->merge(is_array($meta) ? $meta : [])->merge(['title' => $title])" /> @endif
    {{-- The nonce too: its loader is an inline script, blocked by a nonce-based Content-Security-Policy. --}}
    @if ($googleFonts) <tk:google.fonts :attributes="$attributes->prefixed('google-fonts:')->merge($googleFonts)->merge(['noscript' => false, 'nonce' => $nonce])" /> @endif
    <title>{{ $title }}</title>
    {{ $head ?? '' }}
    @if ($appearance) <tk:appearance :nonce="is_string($appearance) ? $appearance : $nonce" /> @endif
    @if ($consent && (($gtag === true ? config('services.google.gtag') : $gtag) || ($gtm === true ? config('services.google.gtm') : $gtm)))
        <tk:google.consent :$nonce />
    @endif
    @if ($gtag) <tk:google.gtag :id="$gtag" :$nonce /> @endif
    @if ($gtm) <tk:google.gtm :id="$gtm" :$nonce /> @endif
    @if ($typekit) <link href="https://use.typekit.net/{{ $typekit }}.css" rel="stylesheet" @if ($nonce) nonce="{{ $nonce }}" @endif> @endif
    @foreach ($styles as $style) <link href="{{ $style }}" rel="stylesheet" @if ($nonce) nonce="{{ $nonce }}" @endif> @endforeach
    @if ($stackStyles) @stack($stackStyles) @endif
    @if (Vite::isRunningHot() || Vite::manifestHash($viteBuildDirectory) !== null) @vite($vite, $viteBuildDirectory) @endif
</head>
<body {{
    $attributes
        ->whereDoesntStartWith(['html:', 'head:', 'meta:', 'google-fonts:', 'components:', 'toast:'])
        ->classes(
            'min-h-dvh',
            '[:where(&)]:bg-white dark:[:where(&)]:bg-zinc-900',
            '[:where(&)]:text-zinc-700 dark:[:where(&)]:text-white/70',
            'antialiased',
        )
}}>
    @if ($googleFonts) <tk:google.fonts :attributes="$attributes->prefixed('google-fonts:')->merge($googleFonts)->merge(['noscript' => true])" /> @endif
    {{-- Not with consent on: a <noscript> iframe can't wait for consent. --}}
    @if ($gtm && ! $consent) <tk:google.gtm :id="$gtm" noscript /> @endif
    {{ $slot }}
    @foreach ($components as $c => $component) <x-dynamic-component :attributes="$componentAttributes" :$component /> @endforeach
    @if ($toast && $livewire) @persist('toast') <tk:toast :flashed="false" :attributes="$attributes->prefixed('toast:')" /> @endpersist <tk:toast.flashed :$nonce /> @endif
    @if ($toast && ! $livewire) <tk:toast :attributes="$attributes->prefixed('toast:')" /> @endif
    {!! TALLKit::scripts(['nonce' => $nonce]) !!}
    @foreach ($scripts as $script) <script src="{{ $script }}" @if ($nonce) nonce="{{ $nonce }}" @endif></script> @endforeach
    @if ($stackScripts) @stack($stackScripts) @endif
</body>
</html>
