@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'prepend' => null,
    'description' => null,
    'append' => null,
    'actions' => null,
    'image' => null,
    'showEmail' => true,
])
@php

[$user, $name, $email] = TALLKit::userContext(attributes: $attributes);
$image ??= data_get($user, 'image');
$description ??= data_get($user, 'description', $showEmail ? $email : null);

@endphp
<tk:content
    :attributes="$attributes->prefixed('content:',
        with: ['container:', 'title:' => 'name:', 'description:', 'list:', 'actions:']
    )->classes('items-center')"
    container:class="-space-y-px! min-w-0"
    :$size
    :$prepend
    :title="$name"
    title:class="truncate block max-w-full"
    title:scale="default"
    :$description
    description:class="truncate block max-w-full"
    description:scale="small"
    description:as="span"
    :$append
    :$actions
>
    <x-slot:icon>
        <tk:avatar
            :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->whereDoesntStartWith([
                'content:', 'container:', 'name:', 'description:', 'list:', 'actions:',
            ])
            ->merge(['alt' => ''])"
            :$size
            :src="$image"
        />
    </x-slot:icon>

    {{ $slot }}
</tk:content>
