@props([
    'method' => 'post',
    'enctype' => null,
    'route' => null,
    'action' => null,
    'alert' => null,
    'error-group' => null,
])
@php

$method = strtoupper($method);
$action = in_livewire() ? ($action ?? 'submit') : route_detect(routes: [$route, $action], default: request()->url());
$errorGroup = ${'error-group'} ?? $attributes->pluck('errorGroup');

@endphp
<form {{
    $attributes->dataKey('form')
        ->whereDoesntStartWith(['alert:', 'error-group:', 'submit:'])
        ->classes(
            '[:where(&)]:space-y-6',
            match ($errorGroup) {
                'only' => '[&_[data-tallkit-error]]:hidden',
                default => ''
            }
        )
        ->when(
            in_livewire(),
            fn ($attrs) => $attrs->merge(['wire:submit' => $action]),
            fn ($attrs) => $attrs
                ->merge(!$enctype && Str::contains($slot, 'type="file"', true) ? ['enctype' => 'multipart/form-data'] : [])
                ->merge(['method' => $method])
                ->merge(['action' => $action])
        )
}}>
    @unless (in_livewire())
        @unless (in_array($method, ['HEAD', 'GET', 'OPTIONS']))
            @csrf
        @endunless

        @if (in_array($method, ['PUT', 'PATCH', 'DELETE']))
            @method($method)
        @endif
    @endunless

    @if ($alert !== false)
        <tk:alert.session :attributes="TALLKit::attributesAfter($attributes, 'alert:')">
            {{ $alert ?? '' }}
        </tk:alert.session>
    @endif

    @if ($errorGroup)
        <tk:error.group :attributes="TALLKit::attributesAfter($attributes, 'error-group:')" />
    @endif

    {{ $slot }}

    @if ($action && Str::doesntContain($slot, 'type="submit"', true))
        <tk:submit
            :attributes="TALLKit::attributesAfter($attributes, 'submit:')->classes('w-full')"
            variant="inverse"
        />
    @endif
</form>
