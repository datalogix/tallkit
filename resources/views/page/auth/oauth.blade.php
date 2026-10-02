@props([
    'size' => null,
    'items' => null,
    'route' => null,
    'separator' => null,
])
@php

$icons = [
    'google' => 'logos:google-icon',
    'github' => 'logos:github-icon',
    'gitlab' => 'logos:gitlab',
    'facebook' => 'logos:facebook',
    'apple' => 'simple-icons:apple',
    'microsoft' => 'logos:microsoft-icon',
    'discord' => 'logos:discord-icon',
    'linkedin' => 'logos:linkedin-icon',
    'bitbucket' => 'logos:bitbucket',
    'slack' => 'logos:slack-icon',
    'twitter' => 'simple-icons:x',
    'x' => 'simple-icons:x',
];

$items ??= collect(array_keys($icons))
    ->filter(fn ($provider) => filled(config("services.{$provider}.client_id")))
    ->values()
    ->all();

$items = Arr::wrap($items);

@endphp
@if (filled($items))
    @if ($separator !== false)
        <tk:separator
            :attributes="$attributes->prefixed('separator:')"
            :label="is_string($separator) ? $separator : 'or'"
        />
    @endif

    @foreach ($items as $provider)
        <tk:button
            :attributes="$attributes->prefixed($provider.':')->classes('w-full')"
            :$size
            :href="$oauthHref = route_detect([$route ?? 'auth.oauth.redirect'], parameters: ['provider' => $provider], default: null)"
            :disabled="! $oauthHref"
            :icon="$icons[$provider] ?? 'ph:key'"
            :label="__('Continue with :provider', ['provider' => Str::headline($provider)])"
            variant="outline"
        />
    @endforeach
@endif
