@props([
    'locale' => null,
    'theme' => null,
    'palette' => null,
    'options' => null,
])
<tk:loadable
    x-data="fullCalendar({{ Js::from(['locale' => $locale ?? app()->getLocale(), 'theme' => $theme, 'palette' => $palette, 'options' => $options]) }})"
    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'loadable:')"
>
    <div
        {{ $attributes->whereDoesntStartWith(['loadable:'])->classes('[:where(&)]:w-full [:where(&)]:h-full') }}
        x-init="render()"
    ></div>
</tk:loadable>
