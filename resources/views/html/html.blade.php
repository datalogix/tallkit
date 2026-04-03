@props([
    'lang' => null,
    'title' => null,
    'charset' => 'utf-8',
    'viewport' => 'width=device-width, initial-scale=1',
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
])
@php

$lang ??= app()->getLocale();
$title ??= config('app.name');
$vite = collect($vite)->unique()->filter(fn ($path) => file_exists(base_path($path)))->toArray();
$googleFonts = is_string($googleFonts) ? ['families' => $googleFonts] : $googleFonts;
$livewire ??= class_exists(\Livewire\Livewire::class);

@endphp
<!DOCTYPE html>
<html {{ TALLKit::attributesAfter($attributes, 'html:')->merge(['lang' => Str::replace($lang, '_', '-')]) }}>
<head {{ TALLKit::attributesAfter($attributes, 'head:') }}>
    @if ($charset) <meta charset="{{ $charset }}"> @endif
    @if ($viewport) <meta name="viewport" content="{{ $viewport }}"> @endif
    @if ($csrfToken) <meta name="csrf-token" content="{{ csrf_token() }}"> @endif
    @foreach ($metaTags as $metaName => $metaContent) <meta name="{{ $metaName }}" content="{{ $metaContent }}"> @endforeach
    @if ($meta) <tk:html.meta :attributes="TALLKit::attributesAfter($attributes, 'meta:')->merge(is_array($meta) ? $meta : [])" /> @endif
    @if ($googleFonts) <tk:google.fonts :attributes="TALLKit::attributesAfter($attributes, 'google-fonts:')->merge($googleFonts)->merge(['noscript' => false])" /> @endif
    <title>{{ $title }}</title>
    {{ $head ?? '' }}
    @if ($appearance) <tk:appearance :nonce="is_string($appearance) ? $appearance : null" /> @endif
    @if ($gtag) <tk:google.gtag :id="$gtag" /> @endif
    @if ($gtm) <tk:google.gtm :id="$gtm" /> @endif
    @if ($typekit) <link href="https://use.typekit.net/{{ $typekit }}.css" rel="stylesheet"> @endif
    @foreach ($styles as $style) <link href="{{ $style }}" rel="stylesheet"> @endforeach
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
    @if ($googleFonts) <tk:google.fonts :attributes="TALLKit::attributesAfter($attributes, 'google-fonts:')->merge($googleFonts)->merge(['noscript' => true])" /> @endif
    @if ($gtm) <tk:google.gtm :id="$gtm" noscript /> @endif
    {{ $slot }}
    @foreach ($components as $c => $component) <x-dynamic-component :attributes="TALLKit::attributesAfter($attributes, 'components:')->merge($attrs)" :$component /> @endforeach
    @if ($toast && $livewire) @persist('toast') <tk:toast :attributes="TALLKit::attributesAfter($attributes, 'toast:')" /> @endpersist @endif
    @if ($toast && !$livewire) <tk:toast :attributes="TALLKit::attributesAfter($attributes, 'toast:')" /> @endif
    @foreach ($scripts as $script) <script src="{{ $script }}"></script> @endforeach
    @if ($stackScripts) @stack($stackScripts) @endif
</body>
</html>
