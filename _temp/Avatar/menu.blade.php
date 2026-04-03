@php $attrs = $attributes->whereDoesntStartWith(['dropdown:', 'menu:', 'avatar:', 'profile:', 'menu-separator:']); @endphp

<tk:dropdown :attributes="$attributesAfter('dropdown:')">
    @if ($profile)
        <tk:avatar.profile
            :attributes="$attrs->merge($attributesAfter('profile:')->getAttributes())"
            :$size
         />
    @else
        <tk:avatar
            :attributes="$attrs->merge($attributesAfter('avatar:')->getAttributes())->classes('cursor-pointer hover:opacity-75')"
            :$size
        />
    @endif

    <tk:menu
        :attributes="$attributesAfter('menu:')"
        :$items
        :$size
    >
        <x-slot:prepend>
            @unless ($profile)
                <tk:avatar.profile
                    :attributes="$attrs->merge($attributesAfter('profile:')->getAttributes())"
                    :$size
                />

                <tk:menu.separator :attributes="$attributesAfter('menu-separator:')" />
            @endunless

            {{ $prepend ?? '' }}
        </x-slot:prepend>

        {{ $slot }}
    </tk:menu>
</tk:dropdown>
