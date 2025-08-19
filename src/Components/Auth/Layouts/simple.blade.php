<div {{ $attributes
    ->whereDoesntStartWith(['area:', 'appearance:', 'container:', 'brand:', 'card:'])
    ->classes('min-h-dvh')
}}>
    <div {{ $attributesAfter('area:')->classes('p-4 relative') }}>
        @if ($appearance)
            <tk:appearance.toggle :attributes="$attributesAfter('appearance:')->classes('absolute right-4 top-4 hidden lg:flex')" />
        @endif

        <tk:container :attributes="$attributesAfter('container:')->classes('max-w-xl px-0 flex flex-col justify-center space-y-8 lg:mt-16')">
            @isset ($brand)
                {{ $brand }}
            @else
                <tk:brand
                    :attributes="$attributesAfter('brand:')"
                    :href="false"
                    size="xl"
                />
            @endisset

            <tk:card :attributes="$attributesAfter('card:')->classes('lg:p-6 w-full')">
                {{ $slot }}
            </tk:card>
        </tk:container>
    </div>
</div>
