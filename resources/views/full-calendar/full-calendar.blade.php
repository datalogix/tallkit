@props([
    'options' => null,
])
<tk:loadable
    x-data="fullCalendar({{ Js::from(array_replace_recursive(['locale' => Str::lower(Str::replace('_', '-', app()->getLocale()))], Arr::wrap($options))) }})"
    :attributes="TALLKit::attributesAfter($attributes, 'loadable:')"
>
    <div
        {{ $attributes->whereDoesntStartWith(['loadable:'])->classes('[:where(&)]:w-full [:where(&)]:h-full') }}
        x-init="render"
    ></div>
</tk:loadable>
