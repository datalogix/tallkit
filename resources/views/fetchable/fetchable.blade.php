@props([
    'url' => null,
    'data' => null,
    'autoFetch' => null,
    'chart' => null,
    'options' => null,
])
<tk:loadable
    x-data="fetchable({{ Js::from($url) }}, {{ Js::from($data) }}, {{ Js::from($autoFetch) }}, {{ Js::from($options) }})"
    :attributes="$attributes->whereDoesntStartWith(['chart:', 'json:'])"
>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @elseif ($chart)
        <x-dynamic-component
            :component="'tallkit::chart.'.$chart"
            :attributes="TALLKit::attributesAfter($attributes, 'chart:')"
            x-init="render(data)"
        />
    @else
        <tk:pretty-print-json
            :attributes="TALLKit::attributesAfter($attributes, 'json:')"
            x-html="render(data)"
        />
    @endif
</tk:loadable>
