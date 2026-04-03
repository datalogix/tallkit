@props([
    'appearance' => null,
    'right' => null,
    'bg' => null,
])
@php

$bgs = collect($bg ?? File::glob(public_path('{imgs,images}/{hero/*,heros/*,hero}.{png,jpg,jpeg}'), GLOB_BRACE))
    ->filter()
    ->map(fn ($hero) => asset(str_replace(public_path(), '', $hero)))
    ->unique();

$bg = $bgs->isNotEmpty() ? $bgs->random() : null;
$hasHero = $bg || isset($hero) || TALLKit::attributesAfter($attributes, 'hero:')->isNotEmpty();

@endphp
<div
    {{
        $attributes
            ->whereDoesntStartWith(['area:', 'appearance:', 'container:', 'brand:', 'hero:'])
            ->classes('min-h-dvh grid bg-linear-to-b from-zinc-50 to-white dark:from-zinc-950 dark:to-zinc-800 ')
            ->classes(['lg:grid-cols-2' => $hasHero])
    }}
>
    <div {{ TALLKit::attributesAfter($attributes, 'area:')->classes('p-6 relative', $right ? 'order-last' : 'order-first') }}>
        @if ($appearance !== false)
            <tk:appearance.toggle :attributes="TALLKit::attributesAfter($attributes, 'appearance:')
                ->classes('absolute right-4 top-4')"
            />
        @endif

        <tk:container :attributes="TALLKit::attributesAfter($attributes, 'container:')
            ->classes('max-w-xl px-0 flex flex-col justify-center space-y-8 h-full')"
        >
            @isset ($brand)
                {{ $brand }}
            @else
                <tk:brand
                    :attributes="TALLKit::attributesAfter($attributes, 'brand:')"
                    :href="false"
                    size="xl"
                />
            @endisset

            @if ($hasHero)
                {{ $slot }}
            @else
                <tk:card
                    :attributes="TALLKit::attributesAfter($attributes, 'card:')->classes('
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
        <div {{ TALLKit::attributesAfter($attributes, 'hero:')
            ->classes('hidden lg:flex bg-cover bg-center text-white flex-col')
            ->when($bg, fn ($attr, $value) => $attr->style("background-image: url($value)"))
        }}>
            {{ $hero ?? '' }}
        </div>
    @endif
</div>
