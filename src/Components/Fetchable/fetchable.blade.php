<tk:loadable
    x-data="fetchable({{ Js::from($url) }}, {{ Js::from($data) }}, {{ Js::from($autofetch) }}, {{ $jsonOptions() }})"
    :attributes="$attributes->whereDoesntStartWith(['chart:', 'json:'])"
>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @elseif ($chart)
        <x-dynamic-component
            :component="'tallkit-chart.'.$chart"
            :attributes="$attributesAfter('chart:')"
            x-init="render(data)"
        />
    @else
        <tk:pretty-print-json
            :attributes="$attributesAfter('json:')"
            x-html="render(data)"
        />
    @endif
</tk:loadable>
