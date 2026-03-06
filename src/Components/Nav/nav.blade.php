@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @if (Str::of($slot)->trim()->startsWith('<nav'))
        {{ $slot }}
    @else
        <nav {{
            $attributes->whereDoesntStartWith(['item:'])
                ->classes(
                    'relative flex flex-1 overflow-auto',
                    $fontSize(size: $size, weight: true),
                    $textColor(variant: $variant),
                    $gap(size: $size),
                )
                ->when(
                    $list,
                    fn($attrs) => $attrs->classes('flex-col overflow-visible min-h-auto'),
                    fn($attrs) => $attrs->classes([
                        'items-center', $paddingBlock(size: $size),
                        'overflow-x-auto overflow-y-hidden' => $scrollable
                    ]),
                )
        }}>
            @foreach (collect($items) as $item)
                <tk:nav.item
                    :attributes="$attributesAfter('item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
                />
            @endforeach

            {{ $slot }}
        </nav>
    @endif
@endif
