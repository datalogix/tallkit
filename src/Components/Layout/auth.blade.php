@php
$hasHero = $bg || isset($hero) || count($attributesAfter('hero:')->all()) > 1;
@endphp

<div {{ $attributes
    ->whereDoesntStartWith(['area:', 'appearance:', 'container:', 'brand:', 'hero:'])
    ->classes('min-h-dvh grid dark:bg-linear-to-b dark:from-zinc-950 dark:to-zinc-800')
    ->classes(['lg:grid-cols-2' => $hasHero])
}}>
    <div {{ $attributesAfter('area:')->classes('p-6 relative', $right ? 'order-last' : 'order-first') }}>
        @if ($appearance !== false)
            <tk:appearance.toggle :attributes="$attributesAfter('appearance:')->classes('absolute right-4 top-4 hidden lg:flex')" />
        @endif

        <tk:container :attributes="$attributesAfter('container:')->classes('max-w-xl px-0 flex flex-col justify-center space-y-8 h-full')">
            @isset ($brand)
                {{ $brand }}
            @else
                <tk:brand
                    :attributes="$attributesAfter('brand:')"
                    :href="false"
                    size="xl"
                />
            @endisset

            @if ($hasHero)
                {{ $slot }}
            @else
                <tk:card
                    :attributes="$attributesAfter('card:')->classes('
                        w-full lg:p-6
                        border-none lg:border-solid
                        bg-transparent dark:bg-transparent lg:bg-white dark:lg:bg-zinc-800
                        shadow-none lg:shadow
                    ')"
                >
                    {{ $slot }}
                </tk:card>
            @endif
        </tk:container>
    </div>

    @if ($hasHero)
        <div {{ $attributesAfter('hero:')
            ->classes('hidden lg:flex bg-cover bg-center text-white flex-col')
            ->when($bg, fn ($attr, $value) => $attr->style("background-image: url($value)"))
        }}>
            {{ $hero ?? '' }}
        </div>
    @endif
</div>
