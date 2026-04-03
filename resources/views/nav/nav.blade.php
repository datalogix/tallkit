@props([
    'list' => null,
    'variant' => null,
    'size' => null,
    'scrollable' => null,
    'items' => null,
])
@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @if (Str::of($slot)->trim()->startsWith('<nav'))
        {{ $slot }}
    @else
        <nav {{
            $attributes
                ->dataKey('nav')
                ->whereDoesntStartWith(['item:'])
                ->classes(
                    'relative flex flex-1 overflow-auto',
                    'text-zinc-500 dark:text-white/80',
                    TALLKit::fontSize(size: $size, weight: true),
                    TALLKit::gap(size: $size),
                )
                ->when(
                    $list,
                    fn($attrs) => $attrs->classes('flex-col overflow-visible min-h-auto'),
                    fn($attrs) => $attrs->classes([
                        'items-center',
                        TALLKit::paddingBlock(size: $size),
                        'overflow-x-auto overflow-y-hidden' => $scrollable
                    ]),
                )
        }}>
            @foreach (collect($items) as $item)
                <tk:nav.item
                    :attributes="TALLKit::attributesAfter($attributes, 'item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
                />
            @endforeach

            {{ $slot }}
        </nav>
    @endif
@endif
