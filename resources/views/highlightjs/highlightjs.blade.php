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
        {{-- The slot as text: the highlighter escapes it again. --}}
        x-html="render(@js($code ?? html_entity_decode((string) $slot, ENT_QUOTES | ENT_HTML5)), @js($language))"
    ></code></pre>
</tk:loadable>
