@props([
    'appearance' => null,
    'right' => null,
    'bg' => null,
])
@php

$bgs = collect(
        $bg ??
        Cache::remember(TALLKit::storageKey('auth-hero-images'), 60 * 60 * 24, fn () => collect(['imgs', 'images'])
            ->crossJoin(['hero/*', 'heros/*', 'hero'], ['webp', 'avif', 'png', 'jpg', 'jpeg'])
            ->flatMap(fn ($combo) => File::glob(public_path("{$combo[0]}/{$combo[1]}.{$combo[2]}")))
            ->map(fn ($path) => ltrim(Str::after($path, public_path()), '/\\'))
            ->values()
            ->all()
        )
    )
    ->filter()
    ->map(fn ($hero) => Str::isUrl($hero) ? $hero : asset(ltrim(Str::after($hero, public_path()), '/\\')))
    ->unique()
    ->values();

$bg = null;

if ($bgs->isNotEmpty()) {
    $bg = session()->isStarted() ? session(TALLKit::storageKey('auth-hero')) : null;

    if (! $bgs->contains($bg)) {
        $bg = $bgs->random();

        if (session()->isStarted()) {
            session()->put(TALLKit::storageKey('auth-hero'), $bg);
        }
    }
}

$hasHero = $bg || isset($hero) || $attributes->prefixed('hero:')->isNotEmpty();

@endphp
<div
    {{
        $attributes
            ->whereDoesntStartWith(['area:', 'appearance:', 'container:', 'brand:', 'hero:'])
            ->classes('min-h-dvh grid bg-linear-to-b from-zinc-50 to-white dark:from-zinc-950 dark:to-zinc-800 ')
            ->classes(['lg:grid-cols-2' => $hasHero])
    }}
>
    <main {{ $attributes->prefixed('area:')->classes('p-6 relative', $right ? 'order-last' : 'order-first') }}>
        @if ($appearance !== false)
            <tk:appearance.toggle :attributes="$attributes->prefixed('appearance:')
                ->classes('absolute end-4 top-4')"
            />
        @endif

        <tk:container :attributes="$attributes->prefixed('container:')
            ->classes('[:where(&)]:max-w-xl px-0 flex flex-col justify-center space-y-8 h-full')"
        >
            @isset ($brand)
                {{ $brand }}
            @else
                <tk:brand
                    :attributes="$attributes->prefixed('brand:')"
                    :href="false"
                    size="xl"
                />
            @endisset

            @if ($hasHero)
                {{ $slot }}
            @else
                <tk:card
                    :attributes="$attributes->prefixed('card:')->classes('
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
    </main>

    @if ($hasHero)
        <div {{ $attributes->prefixed('hero:')
            ->classes('hidden lg:flex bg-cover bg-center text-white flex-col')
            ->when($bg, fn ($attrs, $value) => $attrs->style("background-image: url('{$value}')"))
        }}>
            {{ $hero ?? '' }}
        </div>
    @endif
</div>
