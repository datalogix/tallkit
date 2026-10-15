@props([
    'method' => null,
    'enctype' => null,
    'route' => null,
    'action' => null,
    'alert' => null,
    'errorGroup' => null,
    'errorBag' => null,
    'focusError' => null,
    'clearErrorsOnSubmit' => null,
    'toast' => null,
    'errorMessage' => null,
    'successMessage' => null,
    'submit' => null,
])
@php

$method = strtoupper($method ?? 'post');

$wireSubmit = $attributes->whereStartsWith('wire:submit')->first();
$action ??= $wireSubmit ? trim(Str::before($wireSubmit, '(')) : null;
$action = in_livewire() ? ($action ?: 'submit') : route_detect(routes: [$route, $action], default: $action ?: request()->url());

@endphp
<form
    x-data="form({{ Js::from([
        'action' => in_livewire() ? $action : null,
        'focusError' => $focusError !== false,
        'clearErrorsOnSubmit' => $clearErrorsOnSubmit !== false,
        'toast' => $toast ?? ($successMessage ? true : 'error'),
        'errorMessage' => $errorMessage ?? __('There was an error submitting the form.'),
        'successMessage' => $successMessage ?? __('Form submitted successfully.'),
    ]) }})"
    {{
        $attributes
            ->dataKey('form')
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
                fn ($attrs) => $wireSubmit ? $attrs : $attrs->merge(['wire:submit' => $action]),
                fn ($attrs) => $attrs
                    ->mergeDefined(['enctype' => ! $enctype && Str::contains($slot, 'type="file"', true) ? 'multipart/form-data' : null])
                    ->merge(['method' => in_array($method, ['HEAD', 'GET', 'OPTIONS']) ? 'GET' : 'POST'])
                    ->merge(['action' => $action])
            )
    }}
>
    @unless (in_livewire())
        @unless (in_array($method, ['HEAD', 'GET', 'OPTIONS']))
            @csrf
        @endunless

        @if (in_array($method, ['PUT', 'PATCH', 'DELETE']))
            @method($method)
        @endif
    @endunless

    @if ($alert !== false)
        <tk:alert.session :attributes="$attributes->prefixed('alert:')">
            {{ $alert ?? '' }}
        </tk:alert.session>
    @endif

    @if ($errorGroup)
        <tk:error.group :attributes="$attributes->prefixed('error-group:')->dataKey('error-group')" />
    @endif

    {{ $slot }}

    @if ($submit !== false && $action && Str::doesntContain($slot, 'type="submit"', true))
        <tk:submit
            :attributes="$attributes->prefixed('submit:')->classes('w-full')"
            variant="inverse"
        />
    @endif
</form>
