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
        <tk:chart
            :library="$chart"
            :attributes="$attributes->prefixed('chart:')"
            x-effect="render(data)"
        />
    @else
        <tk:pretty-print-json
            :attributes="$attributes->prefixed('json:')"
            x-html="render(data)"
        />
    @endif
</tk:loadable>
