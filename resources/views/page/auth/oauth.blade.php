@props([
    'size' => null,
    'providers' => null,
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

$providers ??= collect(array_keys($icons))
    ->filter(fn ($provider) => filled(config("services.{$provider}.client_id")))
    ->values()
    ->all();

$providers = Arr::wrap($providers);

@endphp
@if (filled($providers))
    @if ($separator !== false)
        <tk:separator
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'separator:')"
            :text="is_string($separator) ? $separator : 'or'"
        />
    @endif

    @foreach ($providers as $provider)
        <tk:button
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: $provider.':')->classes('w-full')"
            :$size
            :href="route_detect([$route ?? 'auth.oauth.redirect'], parameters: ['provider' => $provider], default: '#')"
            :icon="$icons[$provider] ?? 'ph:key'"
            :label="__('Continue with :provider', ['provider' => Str::headline($provider)])"
            variant="outline"
        />
    @endforeach
@endif
