<div {{ $attributes->merge(['data-keep-open' => $keepOpen]) }} x-data x-init="
    Object.defineProperty($el, 'value', {
        /*
        get: (e) => {
            console.log(this.value)
        },
        */
        set: i => {
            console.log('entro', i)
        }
    })
">
    {{ $slot }}
</div>
