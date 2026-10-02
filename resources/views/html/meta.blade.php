@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'author' => null,
    'robots' => 'index, follow',
    'type' => 'website',
    'card' => 'summary_large_image',
    'image' => null,
    'url' => null,
    'locale' => null,
    'canonical' => true,
    'siteName' => null,
    'twitterSite' => null,
    'twitterCreator' => null,
])
@php

$title ??= config('app.name');
$description ??= config('app.description');
$keywords ??= config('app.keywords');
$author ??= config('app.author');
$image ??= TALLKit::findImage('meta-image');
$url ??= url()->current();
$locale ??= app()->getLocale();
$canonical = $canonical === true
    ? (fn ($query) => url()->current().($query ? '?'.Arr::query($query) : ''))(
        Arr::where(request()->query(), fn ($value, $key) => ! preg_match('/^(utm_|fbclid$|gclid$|msclkid$|mc_|_ga$)/', (string) $key))
    )
    : $canonical;
$siteName ??= config('app.name');

@endphp
@if ($title) <meta name="title" content="{{ $title }}"> @endif
@if ($description) <meta name="description" content="{{ $description }}"> @endif
@if ($keywords) <meta name="keywords" content="{{ $keywords }}"> @endif
@if ($author) <meta name="author" content="{{ $author }}"> @endif
@if ($robots) <meta name="robots" content="{{ $robots }}"> @endif
@if ($canonical) <link rel="canonical" href="{{ $canonical }}"> @endif

@if ($type) <meta property="og:type" content="{{ $type }}"> @endif
@if ($url) <meta property="og:url" content="{{ $url }}"> @endif
@if ($locale) <meta property="og:locale" content="{{ $locale }}"> @endif
@if ($siteName) <meta property="og:site_name" content="{{ $siteName }}"> @endif
@if ($title) <meta property="og:title" content="{{ $title }}"> @endif
@if ($description) <meta property="og:description" content="{{ $description }}"> @endif
@if ($image) <meta property="og:image" content="{{ $image }}"> @endif

@if ($card) <meta name="twitter:card" content="{{ $card }}"> @endif
@if ($url) <meta name="twitter:url" content="{{ $url }}"> @endif
@if ($twitterSite) <meta name="twitter:site" content="{{ $twitterSite }}"> @endif
@if ($twitterCreator) <meta name="twitter:creator" content="{{ $twitterCreator }}"> @endif
@if ($title) <meta name="twitter:title" content="{{ $title }}"> @endif
@if ($description) <meta name="twitter:description" content="{{ $description }}"> @endif
@if ($image) <meta name="twitter:image" content="{{ $image }}"> @endif
