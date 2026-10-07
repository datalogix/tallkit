@props([
    'nonce' => null,
])
<style
    {{ $attributes->prefixed('style:')->when($nonce, fn ($attrs, $value) => $attrs->merge(['nonce' => $value])) }}
    data-navigate-once
>
    :root.dark {
        color-scheme: dark;
    }
</style>
<script
    {{ $attributes->prefixed('script:')->when($nonce, fn ($attrs, $value) => $attrs->merge(['nonce' => $value])) }}
    data-navigate-once
>
    (function () {
        var mode = null
        try { mode = window.localStorage.getItem(@js(TALLKit::storageKey('appearance'))) } catch (e) {}
        var dark = mode === 'dark' || (mode !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches)
        document.documentElement.classList.toggle('dark', dark)
    })()

    document.addEventListener(@js(TALLKit::eventName('init')), () => window.tallkit.appearance.init())
</script>
