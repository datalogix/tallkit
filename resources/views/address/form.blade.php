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
            'x-data' => 'addressForm',
            'wire:replace.self' => ''
        ] : [])
    "
>
    <div class="grid gap-6 grid-cols-4 lg:grid-cols-5 mb-6">
        <tk:input
            name="zipcode"
            :attributes="TALLKit::attributesAfter($attributes, 'zipcode:')->dataKey('address-form-zipcode')"
            :$required
            :$size
        />
        <tk:input
            name="address"
            field:class="col-span-3 lg:col-span-2"
            :attributes="TALLKit::attributesAfter($attributes, 'address:')->dataKey('address-form-address')"
            :$required
            :$size
            loading="address"
        />
        <tk:input
            name="number"
            field:class="col-span-2 lg:col-span-1"
            :attributes="TALLKit::attributesAfter($attributes, 'number:')->dataKey('address-form-number')"
            :$required
            :$size
        />
        <tk:input
            name="complement"
            field:class="col-span-2 lg:col-span-1"
            :attributes="TALLKit::attributesAfter($attributes, 'complement:')->dataKey('address-form-complement')"
            :$size
        />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <tk:input
            name="neighborhood"
            :attributes="TALLKit::attributesAfter($attributes, 'neighborhood:')->dataKey('address-form-neighborhood')"
            :$required
            :$size
        />
        <tk:input
            name="city"
            :attributes="TALLKit::attributesAfter($attributes, 'city:')->dataKey('address-form-city')"
            :$required
            :$size
        />
        @if (function_exists('statesBR'))
            <tk:select
                name="state"
                field:class="sm:col-span-2 lg:col-span-1"
                :attributes="TALLKit::attributesAfter($attributes, 'state:')->dataKey('address-form-state')"
                :$required
                :$size
                :options="statesBR()"
            />
        @else
            <tk:input
                name="state"
                field:class="sm:col-span-2 lg:col-span-1"
                :attributes="TALLKit::attributesAfter($attributes, 'state:')->dataKey('address-form-state')"
                :$required
                :$size
            />
        @endif
    </div>
</x-dynamic-component>
