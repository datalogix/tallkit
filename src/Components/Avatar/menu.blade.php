@php $attrs = $attributes->whereDoesntStartWith(['dropdown:', 'menu:', 'avatar:', 'profile:']); @endphp
<tk:dropdown :attributes="$attributesAfter('dropdown:')">
    @if ($profile)
        <tk:avatar.profile :attributes="$attrs->merge($attributesAfter('profile:')->getAttributes())" />
    @else
        <tk:avatar :attributes="$attrs->merge($attributesAfter('avatar:')->getAttributes())->classes('cursor-pointer')" />
    @endif

    <tk:menu
        :attributes="$attributesAfter('menu:')"
        :$items
    >
        @unless ($profile)
            <x-slot:prepend>
                <tk:avatar.profile :attributes="$attrs->merge($attributesAfter('profile:')->getAttributes())" />

                <tk:menu.separator />
            </x-slot:prepend>
        @endunless

        {{ $slot }}
    </tk:menu>
</tk:dropdown>
