<x-dynamic-component
    :component="$card ? 'tallkit-card' : 'tallkit-section'"
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
            :attributes="$attributesAfter('zipcode:')"
            :$required
            :$size
        />
        <tk:input
            name="address"
            field:class="col-span-3 lg:col-span-2"
            :attributes="$attributesAfter('address:')"
            :$required
            :$size
            loading
            loading:class="hidden [.field-loading_&]:hidden"
        />
        <tk:input
            name="number"
            field:class="col-span-2 lg:col-span-1"
            :attributes="$attributesAfter('number:')"
            :$required
            :$size
        />
        <tk:input
            name="complement"
            field:class="col-span-2 lg:col-span-1"
            :attributes="$attributesAfter('complement:')"
            :$size
        />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <tk:input
            name="neighborhood"
            :attributes="$attributesAfter('neighborhood:')"
            :$required
            :$size
        />
        <tk:input
            name="city"
            :attributes="$attributesAfter('city:')"
            :$required
            :$size
        />
        @if (function_exists('statesBR'))
            <tk:select
                name="state"
                field:class="sm:col-span-2 lg:col-span-1"
                :attributes="$attributesAfter('state:')"
                :$required
                :$size
                :options="statesBR()"
            />
        @else
            <tk:input
                name="state"
                field:class="sm:col-span-2 lg:col-span-1"
                :attributes="$attributesAfter('state:')"
                :$required
                :$size
            />
        @endif
    </div>
</x-dynamic-component>

