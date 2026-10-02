@props([
    'size' => null,
    'required' => null,
])
<div class="grid gap-6 grid-cols-4 lg:grid-cols-5 mb-6">
    <tk:input
        name="zipcode"
        autocomplete="postal-code"
        field:class="col-span-2 sm:col-span-1"
        :attributes="$attributes->prefixed('zipcode:')->dataKey('address-form-zipcode')"
        :$required
        :$size
    />
    <tk:input
        name="address"
        autocomplete="address-line1"
        field:class="col-span-4 sm:col-span-3 lg:col-span-2"
        :attributes="$attributes->prefixed('address:')->dataKey('address-form-address')"
        :$required
        :$size
        loading="address"
    />
    <tk:input
        name="number"
        field:class="col-span-2 lg:col-span-1"
        :attributes="$attributes->prefixed('number:')->dataKey('address-form-number')"
        :$required
        :$size
    />
    <tk:input
        name="complement"
        autocomplete="address-line2"
        field:class="col-span-2 lg:col-span-1"
        :attributes="$attributes->prefixed('complement:')->dataKey('address-form-complement')"
        :$size
    />
</div>
<div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
    <tk:input
        name="neighborhood"
        autocomplete="address-level3"
        :attributes="$attributes->prefixed('neighborhood:')->dataKey('address-form-neighborhood')"
        :$required
        :$size
    />
    <tk:input
        name="city"
        autocomplete="address-level2"
        :attributes="$attributes->prefixed('city:')->dataKey('address-form-city')"
        :$required
        :$size
    />
    @if (function_exists('statesBR'))
        <tk:select
            name="state"
        autocomplete="address-level1"
            field:class="col-span-2 lg:col-span-1"
            :attributes="$attributes->prefixed('state:')->dataKey('address-form-state')"
            :$required
            :$size
            :options="statesBR()"
        />
    @else
        <tk:input
            name="state"
        autocomplete="address-level1"
            field:class="col-span-2 lg:col-span-1"
            :attributes="$attributes->prefixed('state:')->dataKey('address-form-state')"
            :$required
            :$size
        />
    @endif
</div>
