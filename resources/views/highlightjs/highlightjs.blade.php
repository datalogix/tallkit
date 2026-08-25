@props([
    'code' => null,
    'language' => null,
])
<tk:loadable
    x-data="highlightjs"
    :attributes="TALLKit::attributesAfter($attributes, 'loadable:')"
>
    <pre><code
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-html="render(@js($code ?? $slot), @js($language))"
    ></code></pre>
</tk:loadable>
