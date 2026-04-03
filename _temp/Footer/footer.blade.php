<footer {{
    $attributes->whereDoesntStartWith(['container:'])
        ->classes('[grid-area:footer]')
        ->classes(['p-6 lg:p-8' => !$container])
    }}
>
    <tk:container.wrapper
        :attributes="$attributesAfter('container:')->classes('p-6 lg:p-8')"
        :$container
    >
        {{ $slot }}
    </tk:container.wrapper>
</footer>
