<style
    {{ $attributesAfter('style:')->when($nonce, fn ($attrs, $value) => $attrs->merge(['nonce' => $value])) }}
    data-navigate-once
>
    :root.dark {
        color-scheme: dark;
    }
</style>

<script
    {{ $attributesAfter('script:')->when($nonce, fn ($attrs, $value) => $attrs->merge(['nonce' => $value])) }}
    data-navigate-once
>
    document.addEventListener('tallkit:init', () => window.tallkit.appearance.init())
</script>
