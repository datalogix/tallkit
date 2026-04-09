@props([
    'size' => null,
    'prepend' => null,
    'description' => null,
    'append' => null,
    'actions' => null,
    'image' => null,
    'displayEmail' => true,
])
@php

[$user, $name, $email] = TALLKit::resolveUserContext($attributes);
$image ??= data_get($user, 'image');
$description ??= data_get($user, 'description', $displayEmail ? $email : null);

@endphp
<tk:content
    :attributes="TALLKit::attributesAfter(
        $attributes,
        'content:',
        prepend: ['container:', 'title:' => 'name:', 'description:', 'list:', 'actions:']
    )->classes('items-center')"
    container:class="-space-y-px!"
    :$size
    :$prepend
    :title="$name"
    title:class="truncate block"
    title:mode="default"
    :$description
    description:class="truncate block"
    description:mode="small"
    description:as="span"
    :$append
    :$actions
>
    <x-slot:icon>
        <tk:avatar
            :attributes="$attributes->whereDoesntStartWith([
                'content:', 'container:', 'name:', 'description:', 'list:', 'actions:',
            ])"
            :$size
            :src="$image"
        />
    </x-slot:icon>

    {{ $slot }}
</tk:content>
