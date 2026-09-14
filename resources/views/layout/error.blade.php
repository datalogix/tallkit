<div
    {{
        $attributes->whereDoesntStartWith(['appearance:', 'brand:', 'container:'])
            ->classes(
                '
                    relative p-6 overflow-hidden
                    flex min-h-dvh flex-col items-center justify-center
                    bg-linear-to-b from-zinc-50 to-white
                    dark:from-zinc-950 dark:to-zinc-800
                    space-y-10
                '
            )
    }}
>
    @isset ($brand)
        {{ $brand }}
    @else
        <tk:brand
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'brand:')"
            :href="false"
            size="xl"
        />
    @endisset

    <tk:container
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:')"
        size="xs"
    >
        {{ $slot }}
    </tk:container>
</div>
