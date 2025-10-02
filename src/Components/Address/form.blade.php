<div {{
    $attributes
        ->whereDoesntStartWith([
            'zipcode:', 'address:', 'number:', 'complement:',
            'neighborhood:', 'city:', 'state:'
        ])
        ->classes('space-y-6')
        ->merge($autocomplete !== false ? [
            'x-data' => 'addressForm',
            'wire:replace.self' => ''
        ] : [])
}}>
    <div class="grid gap-6 grid-cols-4 lg:grid-cols-5">
        <tk:input
            name="zipcode"
            :attributes="$attributesAfter('zipcode:')"
            :$required
        />
        <tk:input
            name="address"
            field:class="col-span-3 lg:col-span-2"
            :attributes="$attributesAfter('address:')"
            :$required
            loading
            loading:class="hidden"
            :loading:wire:loading="false"
        />
        <tk:input
            name="number"
            field:class="col-span-2 lg:col-span-1"
            :attributes="$attributesAfter('number:')"
            :$required
        />
        <tk:input
            name="complement"
            field:class="col-span-2 lg:col-span-1"
            :attributes="$attributesAfter('complement:')"
        />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <tk:input
            name="neighborhood"
            :attributes="$attributesAfter('neighborhood:')"
            :$required
        />
        <tk:input
            name="city"
            :attributes="$attributesAfter('city:')"
            :$required
        />
        <tk:select
            name="state"
            field:class="sm:col-span-2 lg:col-span-1"
            :attributes="$attributesAfter('state:')"
            :$required
            :options="statesBR()"
        />
    </div>
</div>

