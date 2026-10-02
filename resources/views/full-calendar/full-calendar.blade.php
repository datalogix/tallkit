@props([
    'locale' => null,
    'theme' => null,
    'palette' => null,
    'options' => null,
])
<tk:loadable
    x-data="fullCalendar({{ Js::from(['locale' => TALLKit::resolveLocale($locale), 'theme' => $theme, 'palette' => $palette, 'options' => $options]) }})"
    :attributes="$attributes->prefixed('loadable:')"
>
    <div
        {{ $attributes->whereDoesntStartWith(['loadable:'])->classes('[:where(&)]:w-full [:where(&)]:h-full') }}
        x-init="render()"
    ></div>
</tk:loadable>
{{-- Outside wire:ignore, so each Livewire update brings them. --}}
<script type="application/json" {{ TALLKit::dataKey('options') }}>{!! Illuminate\Support\Js::encode($options) !!}</script>
