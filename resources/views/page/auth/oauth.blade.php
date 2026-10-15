@props([
    'size' => null,
    'items' => null,
    'route' => null,
    'separator' => null,
    'label' => 'Continue with :provider',
    'error' => 'oauth',
])
@php

$items ??= collect(array_keys(TALLKit::oauthIcons()))
    ->filter(fn ($provider) => filled(config("services.{$provider}.client_id")))
    ->values()
    ->all();

$items = collect(Arr::wrap($items))
    ->mapWithKeys(fn ($value, $key) => is_string($key)
        ? [$key => $value]
        : [$value => route_detect([$route ?? 'auth.oauth.redirect'], parameters: ['provider' => $value], default: null)])
    ->all();

@endphp
@error($error)
    <tk:alert :attributes="$attributes->prefixed('error:')" type="danger" :$message />
@enderror

@if (filled($items))
    @if ($separator !== false)
        <tk:separator
            :attributes="$attributes->prefixed('separator:')"
            :label="string_value($separator, 'or')"
        />
    @endif

    @foreach ($items as $provider => $href)
        <tk:button
            :attributes="$attributes->prefixed($provider.':')->classes('w-full')"
            :$size
            :$href
            :disabled="! $href"
            :icon="TALLKit::oauthIcon($provider)"
            :label="__($label, ['provider' => TALLKit::oauthName($provider)])"
            variant="outline"
        />
    @endforeach
@endif
