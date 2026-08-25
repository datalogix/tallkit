@props([
    'url' => null,
    'data' => null,
    'auto' => null,
    'chart' => null,
    'options' => null,
])
<tk:loadable
    x-data="fetchable({{ Js::from(['url' => $url, 'data' => $data, 'auto' => $auto, 'options' => $options]) }})"
    :attributes="$attributes->whereDoesntStartWith(['chart:', 'json:'])"
>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @elseif ($chart)
        <x-dynamic-component
            :component="'tallkit::chart.'.$chart"
            :attributes="TALLKit::attributesAfter($attributes, 'chart:')"
            x-effect="render(data)"
        />
    @else
        <tk:pretty-print-json
            :attributes="TALLKit::attributesAfter($attributes, 'json:')"
            x-html="render(data)"
        />
    @endif
</tk:loadable>
