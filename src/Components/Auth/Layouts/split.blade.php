<div {{ $attributes
    ->whereDoesntStartWith(['area:', 'appearance:', 'container:', 'brand:', 'hero:'])
    ->classes('min-h-dvh grid lg:grid-cols-2 dark:bg-linear-to-b dark:from-zinc-950 dark:to-zinc-850')
}}>
    <div {{ $attributesAfter('area:')->classes('p-4 relative', $right ? 'order-last' : 'order-first') }}>
        @if ($appearance)
            <tk:appearance.toggle :attributes="$attributesAfter('appearance:')->classes('absolute right-4 top-4 hidden lg:flex')" />
        @endif

        <tk:container :attributes="$attributesAfter('container:')->classes('max-w-xl px-0 flex flex-col justify-center space-y-8 lg:mt-16')">
            @isset ($brand)
                {{ $brand }}
            @else
                <tk:brand
                    :attributes="$attributesAfter('brand:')->classes('place-self-start')"
                    :href="false"
                    size="xl"
                />
            @endisset

            {{ $slot }}
        </tk:container>
    </div>
    <div {{ $attributesAfter('hero:')
        ->classes('hidden lg:flex bg-cover bg-center bg-zinc-900 text-white flex-col')
        ->when($bg, fn ($attr, $value) => $attr->style('background-image: url('.$value.')'))
    }}>
        {{ $hero ?? '' }}
    </div>
</div>
