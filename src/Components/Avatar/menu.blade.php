@php $attrs = $attributes->whereDoesntStartWith(['dropdown:', 'menu:', 'avatar:', 'profile:']); @endphp

<tk:dropdown :attributes="$attributesAfter('dropdown:')">
    @if ($profile)
        <tk:avatar.profile
            :attributes="$attrs->merge($attributesAfter('profile:')->getAttributes())"
            :$size
         />
    @else
        <tk:avatar
            :attributes="$attrs->merge($attributesAfter('avatar:')->getAttributes())->classes('cursor-pointer')"
            :$size
        />
    @endif

    <tk:menu
        :attributes="$attributesAfter('menu:')"
        :$items
        :$size
    >
        @unless ($profile)
            <x-slot:prepend>
                <tk:avatar.profile
                    :attributes="$attrs->merge($attributesAfter('profile:')->getAttributes())"
                    :$size
                />

                @if ($slot->isNotEmpty() || collect($items)->isNotEmpty())
                    <tk:menu.separator />
                @endif
            </x-slot:prepend>
        @endunless

        {{ $slot }}
    </tk:menu>
</tk:dropdown>
