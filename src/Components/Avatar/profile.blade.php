<tk:content
    :attributes="$attributesAfter(
           'content:',  prepend: ['container:', 'title:' => 'name:', 'description:', 'list:', 'actions:',]
        )
        ->classes('items-center')
    "
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
