@props([
    'card' => null,
    'size' => null,
    'autocomplete' => null,
    'required' => null,
])
<x-dynamic-component
    :component="$card ? 'tallkit::card' : 'tallkit::section'"
    :attributes="$attributes
        ->whereDoesntStartWith([
            'zipcode:', 'address:', 'number:', 'complement:',
            'neighborhood:', 'city:', 'state:'
        ])
        ->merge($autocomplete !== false ? [
            'x-data' => 'addressForm(' . Js::from(is_array($autocomplete) ? $autocomplete : []) . ')',
            'wire:replace.self' => ''
        ] : [])
    "
    :$size
>
    <div class="grid gap-6 grid-cols-4 lg:grid-cols-5 mb-6">
        <tk:input
            name="zipcode"
            field:class="col-span-2 sm:col-span-1"
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'zipcode:')->dataKey('address-form-zipcode')"
            :$required
            :$size
        />
        <tk:input
            name="address"
            field:class="col-span-4 sm:col-span-3 lg:col-span-2"
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'address:')->dataKey('address-form-address')"
            :$required
            :$size
            loading="address"
        />
        <tk:input
            name="number"
            field:class="col-span-2 lg:col-span-1"
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'number:')->dataKey('address-form-number')"
            :$required
            :$size
        />
        <tk:input
            name="complement"
            field:class="col-span-2 lg:col-span-1"
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'complement:')->dataKey('address-form-complement')"
            :$size
        />
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
        <tk:input
            name="neighborhood"
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'neighborhood:')->dataKey('address-form-neighborhood')"
            :$required
            :$size
        />
        <tk:input
            name="city"
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'city:')->dataKey('address-form-city')"
            :$required
            :$size
        />
        @if (function_exists('statesBR'))
            <tk:select
                name="state"
                field:class="col-span-2 lg:col-span-1"
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'state:')->dataKey('address-form-state')"
                :$required
                :$size
                :options="statesBR()"
            />
        @else
            <tk:input
                name="state"
                field:class="col-span-2 lg:col-span-1"
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'state:')->dataKey('address-form-state')"
                :$required
                :$size
            />
        @endif
    </div>
</x-dynamic-component>
