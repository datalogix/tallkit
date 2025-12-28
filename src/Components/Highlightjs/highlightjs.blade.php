<tk:loadable
    x-data="highlightjs"
    :attributes="$attributesAfter('loadable:')"
>
    <pre><code
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-html="render(@js($slot ?? $code), @js($language))"
    ></code></pre>
</tk:loadable>
