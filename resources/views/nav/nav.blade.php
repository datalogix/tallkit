@props([
    'list' => null,
    'variant' => null,
    'size' => null,
    'scrollable' => null,
    'items' => null,
    'indicator' => null,
    'label' => null,
])
@php

$list = (bool) $list;
$persistKey = 'nav-indicator-'.($attributes->get('id') ?? TALLKit::stableId('nav'));

@endphp
@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @if (Str::of($slot)->trim()->startsWith('<'.'nav'))
        {{ $slot }}
    @else
        <nav
            {{
                $attributes
                    ->dataKey('nav')
                    ->whereDoesntStartWith(['item:', 'indicator:'])
                    ->merge(['aria-label' => $label ? __($label) : null])
                    ->classes(
                        '
                            relative flex flex-1 overflow-auto
                            border-zinc-800/10 dark:border-white/10
                        ',
                        TALLKit::textNeutral(),
                        TALLKit::fontSize(size: $size, weight: true),
                        TALLKit::gap(size: $size),
                    )
                    ->classes(match ($list) {
                        true => [
                            'flex-col overflow-visible min-h-auto',
                            match ($indicator) {
                                'line-left' => 'pl-2 border-l-2',
                                'line-right' => 'pr-2.5 border-r-2',
                                default => '',
                            },
                        ],
                        default => [
                            'flex-row items-center',
                            'overflow-x-auto overflow-y-hidden' => $scrollable,
                            match ($indicator) {
                                'line-top' => 'pt-3',
                                'line-bottom' => 'pb-3',
                                default => '',
                            }
                        ],
                    })
            }}
        >
            @foreach (collect($items) as $item)
                <tk:nav.item
                    :attributes="$attributes->prefixed('item:')
                        ->merge(TALLKit::attributesFromItem($item), false)
                    "
                    :$size
                />
            @endforeach

            {{ $slot }}
        </nav>

        @if ($indicator !== false)
            @persist($persistKey)
                <tk:nav.indicator
                    :attributes="$attributes->prefixed('indicator:')"
                    :mode="match ($list) {
                        true => in_array($indicator, ['line-left', 'line-right']) ? $indicator : 'bg',
                        default => in_array($indicator, ['line-top', 'line-bottom']) ? $indicator : 'bg',
                    }"
                />
            @endpersist
        @endif
    @endif
@endif
