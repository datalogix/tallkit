@props([
    'code' => null,
    'language' => null,
])
<tk:loadable
    x-data="highlightjs"
    :attributes="$attributes->prefixed('loadable:')"
>
    <pre><code
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-html="render({{ Js::from($code ?? html_entity_decode((string) $slot, ENT_QUOTES | ENT_HTML5)) }}, {{ Js::from($language) }})"
    ></code></pre>
</tk:loadable>
