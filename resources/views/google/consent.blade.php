@props([
    'nonce' => null,
])
@once
    <script @if ($nonce) nonce="{{ $nonce }}" @endif>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('consent', 'default', {
            ad_storage: 'denied',
            ad_user_data: 'denied',
            ad_personalization: 'denied',
            analytics_storage: 'denied',
            wait_for_update: 500,
        });
        (function () {
            var state = null
            try { state = window.localStorage.getItem(@js(TALLKit::storageKey('consent'))) } catch (e) {}
            if (state === 'granted' || state === 'denied') {
                gtag('consent', 'update', { ad_storage: state, ad_user_data: state, ad_personalization: state, analytics_storage: state });
            }
        })();
    </script>
@endonce
