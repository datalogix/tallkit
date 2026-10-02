@props([
    'profile' => null,
    'items' => null,
    'size' => null,
    'keepOpen' => null,
    'animation' => null,
])
@php

$attrs = $attributes->whereDoesntStartWith(['dropdown:', 'menu:', 'avatar:', 'profile:', 'menu-separator:', 'trigger:']);

@endphp
<tk:dropdown :attributes="$attributes->prefixed('dropdown:')">
    <button
        type="button"
        {{
            $attributes->prefixed('trigger:')
                ->merge(['aria-label' => $profile ? null : __('Account menu')])
                ->classes(
                    '
                        cursor-pointer hover:opacity-75 text-start
                        outline-none focus-visible:tk-focus-outline
                    ',
                    $profile ? 'rounded-lg' : 'rounded-full',
                )
        }}
    >
        @if ($profile)
            <tk:avatar.profile
                :attributes="TALLKit::attributesMerge($attrs, $attributes->prefixed('profile:'))"
                :$size
            />
        @else
            <tk:avatar
                :attributes="TALLKit::attributesMerge($attrs, $attributes->prefixed('avatar:'))"
                :$size
            />
        @endif
    </button>

    <tk:menu
        :attributes="$attributes->prefixed('menu:')"
        :$items
        :$size
        :$keepOpen
        :$animation
    >
        <x-slot:prepend>
            @unless ($profile)
                <tk:avatar.profile
                    :attributes="TALLKit::attributesMerge($attrs, $attributes->prefixed('profile:'))"
                    :$size
                />

                <tk:menu.separator :attributes="$attributes->prefixed('menu-separator:')" />
            @endunless

            {{ $prepend ?? '' }}
        </x-slot:prepend>

        {{ $slot }}
    </tk:menu>
</tk:dropdown>
