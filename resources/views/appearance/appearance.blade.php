@props([
    'nonce' => null,
])
<style
    {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'style:')->when($nonce, fn ($attrs, $value) => $attrs->merge(['nonce' => $value])) }}
    data-navigate-once
>
    :root.dark {
        color-scheme: dark;
    }
</style>
<script
    {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'script:')->when($nonce, fn ($attrs, $value) => $attrs->merge(['nonce' => $value])) }}
    data-navigate-once
>
    document.addEventListener('tallkit:init', () => window.tallkit.appearance.init())
</script>
