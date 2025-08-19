@php $attrs = $attributes->whereDoesntStartWith(['dropdown:', 'menu:', 'item:', 'avatar:', 'profile:']); @endphp
<tk:dropdown :attributes="$attributesAfter('dropdown:')">
    @if ($profile)
        <tk:avatar.profile :attributes="$attrs->merge($attributesAfter('profile:')->getAttributes())"
        />
    @else
        <tk:avatar :attributes="$attrs->merge($attributesAfter('avatar:')->getAttributes())->classes('cursor-pointer')" />
    @endif

    <tk:menu :attributes="$attributesAfter('menu:')">
        @unless ($profile)
            <tk:avatar.profile :attributes="$attrs->merge($attributesAfter('profile:')->getAttributes())" />

            <tk:menu.separator />
        @endunless

        @foreach (collect($items) as $item)
            <tk:menu.item :attributes="$attributesAfter('item:')->merge($item)" />
        @endforeach

        {{ $slot }}
    </tk:menu>
</tk:dropdown>
