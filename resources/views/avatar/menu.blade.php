@props([
    'profile' => null,
    'items' => null,
    'size' => null,
])
@php

$attrs = $attributes->whereDoesntStartWith(['dropdown:', 'menu:', 'avatar:', 'profile:', 'menu-separator:']);

@endphp
<tk:dropdown :attributes="TALLKit::attributesAfter($attributes, 'dropdown:')">
    @if ($profile)
        <tk:avatar.profile
            :attributes="$attrs->merge(TALLKit::attributesAfter($attributes, 'profile:')->getAttributes())"
            :$size
         />
    @else
        <tk:avatar
            :attributes="$attrs->merge(TALLKit::attributesAfter($attributes, 'avatar:')->getAttributes())->classes('cursor-pointer hover:opacity-75')"
            :$size
        />
    @endif

    <tk:menu
        :attributes="TALLKit::attributesAfter($attributes, 'menu:')"
        :$items
        :$size
    >
        <x-slot:prepend>
            @unless ($profile)
                <tk:avatar.profile
                    :attributes="$attrs->merge(TALLKit::attributesAfter($attributes, 'profile:')->getAttributes())"
                    :$size
                />

                <tk:menu.separator :attributes="TALLKit::attributesAfter($attributes, 'menu-separator:')" />
            @endunless

            {{ $prepend ?? '' }}
        </x-slot:prepend>

        {{ $slot }}
    </tk:menu>
</tk:dropdown>
