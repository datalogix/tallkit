@props([
    'size' => null,
    'required' => null,
    'name' => null,
])
@php

$field = fn (string $key) => filled($name) ? $name.'['.$key.']' : $key;
$busy = 'has-[[inert]]:opacity-disabled';

@endphp
<div class="grid gap-6 grid-cols-4 lg:grid-cols-5 mb-6">
    <tk:input
        :name="$field('zipcode')"
        autocomplete="postal-code"
        field:class="col-span-2 sm:col-span-1"
        :attributes="$attributes->prefixed('zipcode:')->dataKey('address-form-zipcode')"
        :$required
        :$size
    />
    <tk:input
        :name="$field('address')"
        autocomplete="address-line1"
        field:class="col-span-4 sm:col-span-3 lg:col-span-2 {{ $busy }}"
        :attributes="$attributes->prefixed('address:')->dataKey('address-form-address')->merge([
            'loading:wire:loading.class.remove' => false,
            'loading:wire:target' => false,
        ])"
        :$required
        :$size
        loading="address"
    />
    <tk:input
        :name="$field('number')"
        field:class="col-span-2 lg:col-span-1"
        :attributes="$attributes->prefixed('number:')->dataKey('address-form-number')"
        :$required
        :$size
    />
    <tk:input
        :name="$field('complement')"
        autocomplete="address-line2"
        field:class="col-span-2 lg:col-span-1"
        :attributes="$attributes->prefixed('complement:')->dataKey('address-form-complement')"
        :$size
    />
    <tk:error
        :attributes="$attributes
            ->prefixed('error:')
            ->dataKey('address-form-error')
                ->merge([
                'id' => TALLKit::stableId('address-form', $name, 'error'),
                'hidden' => true,
            ])
            ->classes('col-span-full -mt-4')
        "
        :message="__('Zipcode not found.')"
        :$size
    />
</div>
<div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
    <tk:input
        :name="$field('neighborhood')"
        autocomplete="address-level3"
        field:class="{{ $busy }}"
        :attributes="$attributes->prefixed('neighborhood:')->dataKey('address-form-neighborhood')"
        :$required
        :$size
    />
    <tk:input
        :name="$field('city')"
        autocomplete="address-level2"
        field:class="{{ $busy }}"
        :attributes="$attributes->prefixed('city:')->dataKey('address-form-city')"
        :$required
        :$size
    />
    @if ($attributes->has('state:options') || function_exists('statesBR'))
        <tk:select
            :name="$field('state')"
            autocomplete="address-level1"
            field:class="col-span-2 lg:col-span-1 {{ $busy }}"
            :attributes="$attributes->prefixed('state:')->dataKey('address-form-state')"
            :$required
            :$size
            :options="function_exists('statesBR') ? statesBR() : []"
        />
    @else
        <tk:input
            :name="$field('state')"
            autocomplete="address-level1"
            field:class="col-span-2 lg:col-span-1 {{ $busy }}"
            :attributes="$attributes->prefixed('state:')->dataKey('address-form-state')"
            :$required
            :$size
        />
    @endif
</div>
